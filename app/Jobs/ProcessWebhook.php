<?php

namespace App\Jobs;

use App\Models\Order;
use App\Models\WebhookLog;
use App\Models\WalletTransaction;
use App\Services\FlutterwaveService;
use App\Services\NotificationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProcessWebhook implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Retry up to 3 times with exponential back-off (60s, 120s, 300s).
     */
    public int $tries = 3;
    public int $backoff = 60;

    public function __construct(private readonly int $webhookLogId)
    {
    }

    public function handle(FlutterwaveService $flutterwave, NotificationService $notifications): void
    {
        $log = WebhookLog::findOrFail($this->webhookLogId);

        // Skip if already handled (duplicate delivery)
        if ($log->status === 'processed') {
            Log::info('ProcessWebhook: already processed, skipping', ['id' => $log->id]);
            return;
        }

        $log->markProcessing();

        try {
            $payload = $log->payload;
            $txData = $payload['data'] ?? [];
            $txRef = $txData['tx_ref'] ?? null;

            // Server-side re-verification — never trust the payload alone
            $verified = $flutterwave->verifyTransaction((string) $txData['id']);

            if (!$verified || $verified['status'] !== 'successful') {
                throw new \RuntimeException("Flutterwave verification failed for tx_ref: {$txRef}");
            }

            $amount = (float) $verified['amount'];

            if (str_starts_with((string) $txRef, 'wallet_topup_')) {
                $this->processWalletTopup($txRef, $amount, $notifications);
            } else {
                $this->processOrderPayment($txRef, $amount, $notifications);
            }

            $log->markProcessed();

        } catch (\Throwable $e) {
            Log::error('ProcessWebhook failed', [
                'webhook_log_id' => $log->id,
                'error' => $e->getMessage(),
                'attempt' => $this->attempts(),
            ]);

            $log->markFailed($e->getMessage());

            // Re-throw so Laravel's queue retries (up to $tries times)
            throw $e;
        }
    }

    /**
     * Called after all retries are exhausted.
     */
    public function failed(\Throwable $e): void
    {
        Log::critical('ProcessWebhook permanently failed', [
            'webhook_log_id' => $this->webhookLogId,
            'error' => $e->getMessage(),
        ]);

        WebhookLog::find($this->webhookLogId)?->markFailed(
            'Permanently failed after ' . $this->tries . ' attempts: ' . $e->getMessage()
        );
    }

    // ── Private processors ────────────────────────────────────────────────────

    private function processWalletTopup(string $txRef, float $amount, NotificationService $notifications): void
    {
        // Parse user_id from wallet_topup_{user_id}_{timestamp}
        $parts = explode('_', $txRef);
        $userId = $parts[2] ?? null;

        if (!$userId) {
            throw new \RuntimeException("Could not parse user_id from wallet topup ref: {$txRef}");
        }

        // Idempotency guard — safe to re-run if job retries
        $alreadyDone = WalletTransaction::where('reference', $txRef)->exists();
        if ($alreadyDone) {
            Log::info('ProcessWebhook: wallet topup already applied', ['tx_ref' => $txRef]);
            return;
        }

        DB::transaction(function () use ($userId, $amount, $txRef) {
            \App\Models\User::where('id', $userId)->increment('wallet_balance', $amount);

            WalletTransaction::create([
                'user_id' => $userId,
                'type' => 'credit',
                'amount' => $amount,
                'status' => 'completed',
                'description' => 'Wallet top-up',
                'reference' => $txRef,
            ]);
        });

        $notifications->walletTopup((int) $userId, $amount);
    }

    private function processOrderPayment(string $orderId, float $amount, NotificationService $notifications): void
    {
        $order = Order::find($orderId);

        if (!$order) {
            throw new \RuntimeException("Order not found: {$orderId}");
        }

        // Idempotency guard
        if ($order->payment_status === 'success') {
            Log::info('ProcessWebhook: order payment already applied', ['order_id' => $orderId]);
            return;
        }

        if ($amount < (float) $order->total) {
            throw new \RuntimeException(
                "Payment amount mismatch for order {$orderId}: expected {$order->total}, got {$amount}"
            );
        }

        $order->update([
            'payment_status' => 'success',
            'status' => 'confirmed',
            'payment_reference' => $orderId,
        ]);

        $ref = strtoupper(substr($order->id, 0, 8));
        $notifications->orderConfirmed($order->user_id, $order->id, $ref);
        $notifications->paymentSuccess($order->user_id, (float) $order->total, $ref);
    }
}
