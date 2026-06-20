<?php

namespace App\Jobs;

use App\Models\Order;
use App\Models\User;
use App\Models\WebhookLog;
use App\Models\WalletTransaction;
use App\Services\AppContextService;
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
     * Retry up to 3 times with exponential back-off (60 s → 120 s → 300 s).
     * Each retry re-verifies the transaction with Flutterwave before crediting.
     */
    public int $tries   = 3;
    public int $backoff = 60;

    public function __construct(private readonly int $webhookLogId) {}

    // ─── Entry point ──────────────────────────────────────────────────────────

    public function handle(
        FlutterwaveService $flutterwave,
        NotificationService $notifications,
        AppContextService $appContext,
    ): void {
        $log = WebhookLog::findOrFail($this->webhookLogId);

        // Guard: already processed (e.g. duplicate job dispatch)
        if ($log->status === 'processed') {
            Log::info('ProcessWebhook: already processed — skipping', ['id' => $log->id]);
            return;
        }

        $log->markProcessing();

        try {
            $payload = $log->payload;
            $txData  = $payload['data'] ?? [];
            $txRef   = $txData['tx_ref'] ?? null;
            $txId    = (string) ($txData['id'] ?? '');

            // ── Server-side re-verification — NEVER trust the webhook payload ──
            $verified = $flutterwave->verifyTransaction($txId);

            if (!$verified || $verified['status'] !== 'successful') {
                throw new \RuntimeException(
                    "Flutterwave re-verification failed for tx_ref: {$txRef} (tx_id: {$txId})"
                );
            }

            // ── Route to the correct handler based on tx_ref prefix ───────────
            //
            // Prefixes used in this codebase:
            //   wallet_topup_{user_id}_{timestamp}  — card/link top-up
            //   wallet_va_{user_id}_{timestamp}      — bank transfer (virtual account)
            //   {uuid}                               — order payment (order id is the ref)
            //
            if (str_starts_with((string) $txRef, 'wallet_topup_') ||
                str_starts_with((string) $txRef, 'wallet_va_')) {
                $this->processWalletTopup($txRef, $verified, $notifications, $appContext);
            } else {
                $this->processOrderPayment($txRef, $verified, $notifications, $appContext);
            }

            $log->markProcessed();

        } catch (\Throwable $e) {
            Log::error('ProcessWebhook failed', [
                'webhook_log_id' => $log->id,
                'error'          => $e->getMessage(),
                'attempt'        => $this->attempts(),
            ]);

            $log->markFailed($e->getMessage());

            // Re-throw so Laravel retries up to $tries times
            throw $e;
        }
    }

    // ─── Permanent failure (all retries exhausted) ────────────────────────────

    public function failed(\Throwable $e): void
    {
        Log::critical('ProcessWebhook permanently failed', [
            'webhook_log_id' => $this->webhookLogId,
            'error'          => $e->getMessage(),
        ]);

        WebhookLog::find($this->webhookLogId)?->markFailed(
            'Permanently failed after ' . $this->tries . ' retries: ' . $e->getMessage()
        );
    }

    // ─── Wallet top-up (card link OR virtual account bank transfer) ───────────

    /**
     * Credits the user's wallet.
     *
     * Works for both:
     *   - wallet_topup_{user_id}_{ts}  (Flutterwave payment link)
     *   - wallet_va_{user_id}_{ts}     (virtual account bank transfer)
     *
     * Safe to run multiple times — the idempotency check on `reference`
     * ensures we never double-credit.
     */
    private function processWalletTopup(
        string $txRef,
        array  $verified,
        NotificationService $notifications,
        AppContextService $appContext,
    ): void {
        // Parse user_id from ref  e.g. "wallet_topup_42_1718000000" → 42
        //                          or  "wallet_va_42_1718000000"     → 42
        $parts  = explode('_', $txRef);
        // wallet_topup_X → parts[2]; wallet_va_X → parts[2]
        $userId = $parts[2] ?? null;

        if (!$userId || !is_numeric($userId)) {
            throw new \RuntimeException(
                "Could not parse user_id from wallet ref: {$txRef}"
            );
        }

        $user = User::find((int) $userId);
        if (!$user) {
            throw new \RuntimeException("User {$userId} not found for wallet ref: {$txRef}");
        }

        // Idempotency: check WalletTransaction by reference
        if (WalletTransaction::where('reference', $txRef)
            ->where('status', 'completed')->exists()) {
            Log::info('ProcessWebhook: wallet topup already applied', ['tx_ref' => $txRef]);
            return;
        }

        $amount   = (float) $verified['amount'];
        $currency = strtoupper($verified['currency'] ?? 'NGN');

        // Basic sanity checks
        if ($amount <= 0) {
            throw new \RuntimeException("Invalid amount {$amount} for ref: {$txRef}");
        }
        if ($currency !== 'NGN') {
            throw new \RuntimeException("Unexpected currency {$currency} for ref: {$txRef}");
        }

        DB::transaction(function () use ($user, $amount, $txRef, $appContext) {
            // Mark any existing pending transaction as completed
            $existing = WalletTransaction::where('reference', $txRef)
                ->where('status', 'pending')->first();

            if ($existing) {
                $existing->update(['status' => 'completed', 'amount' => $amount]);
            } else {
                // Create fresh (e.g. payment link bypassed the /topup endpoint)
                WalletTransaction::create([
                    'user_id'     => $user->id,
                    'type'        => 'credit',
                    'amount'      => $amount,
                    'status'      => 'completed',
                    'description' => 'Wallet top-up',
                    'reference'   => $txRef,
                ]);
            }

            // Recompute balance from transactions table (AppContextService handles this)
            $appContext->updateUserBalance($user->id);
        });

        // Push notification + in-app
        $notifications->walletTopup((int) $userId, $amount);

        Log::info('ProcessWebhook: wallet topped up', [
            'user_id' => $userId,
            'amount'  => $amount,
            'tx_ref'  => $txRef,
        ]);
    }

    // ─── Order payment (card / bank) ─────────────────────────────────────────

    /**
     * Marks an order as paid and triggers order-confirmed notifications.
     * The tx_ref for order payments is the order UUID itself.
     */
    private function processOrderPayment(
        string $orderId,
        array  $verified,
        NotificationService $notifications,
        AppContextService $appContext,
    ): void {
        $order = Order::find($orderId);

        if (!$order) {
            throw new \RuntimeException("Order not found: {$orderId}");
        }

        // Idempotency
        if ($order->payment_status === 'success') {
            Log::info('ProcessWebhook: order already paid', ['order_id' => $orderId]);
            return;
        }

        $paidAmount    = (float) $verified['amount'];
        $expectedTotal = (float) $order->total;

        // Allow a tiny tolerance (e.g. rounding), but reject clear mismatches
        if ($paidAmount < ($expectedTotal - 1)) {
            throw new \RuntimeException(
                "Underpayment for order {$orderId}: expected ₦{$expectedTotal}, got ₦{$paidAmount}"
            );
        }

        DB::transaction(function () use ($order, $orderId, $verified) {
            $order->update([
                'payment_status'    => 'success',
                'status'            => 'confirmed',
                'payment_reference' => $verified['flw_ref'] ?? $orderId,
            ]);
        });

        $ref = strtoupper(substr($order->id, 0, 8));
        $notifications->orderConfirmed($order->user_id, $order->id, $ref);
        $notifications->paymentSuccess($order->user_id, $expectedTotal, $ref);

        Log::info('ProcessWebhook: order payment processed', [
            'order_id'    => $orderId,
            'paid_amount' => $paidAmount,
        ]);
    }
}
