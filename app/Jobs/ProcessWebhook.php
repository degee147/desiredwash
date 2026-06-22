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
            //   wallet_va_{user_id}                  — bank transfer (permanent virtual account)
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
     *   - wallet_va_{user_id}           (permanent virtual account bank transfer)
     *
     * For permanent VAs, each transfer from Flutterwave arrives with a unique
     * flw_ref but the same tx_ref — so we use flw_ref for idempotency instead
     * of tx_ref to allow multiple top-ups to the same permanent account.
     */
    private function processWalletTopup(
        string $txRef,
        array  $verified,
        NotificationService $notifications,
        AppContextService $appContext,
    ): void {
        // Parse user_id from ref:
        //   wallet_topup_42_1718000000 → parts[2] = 42
        //   wallet_va_42               → parts[2] = 42
        $parts  = explode('_', $txRef);
        $userId = $parts[2] ?? null;

        if (!$userId || !is_numeric($userId)) {
            throw new \RuntimeException(
                "Could not parse user_id from wallet ref: {$txRef}"
            );
        }

        $user = \App\Models\User::find((int) $userId);
        if (!$user) {
            throw new \RuntimeException("User {$userId} not found for wallet ref: {$txRef}");
        }

        // For permanent VAs: use flw_ref for idempotency — the same tx_ref
        // (wallet_va_{id}) arrives with every transfer to that account,
        // so tx_ref alone cannot distinguish individual top-ups.
        $isPermanentVa = str_starts_with($txRef, 'wallet_va_') && !str_contains($txRef, '_', strlen('wallet_va_') + strlen($userId));
        $idempotencyKey = $isPermanentVa
            ? ($verified['flw_ref'] ?? $txRef)
            : $txRef;

        // Idempotency check
        if (WalletTransaction::where('reference', $idempotencyKey)
            ->where('status', 'completed')->exists()) {
            \Illuminate\Support\Facades\Log::info('ProcessWebhook: wallet topup already applied', ['key' => $idempotencyKey]);
            return;
        }

        $amount   = (float) $verified['amount'];
        $currency = strtoupper($verified['currency'] ?? 'NGN');

        if ($amount <= 0) {
            throw new \RuntimeException("Invalid amount {$amount} for ref: {$txRef}");
        }
        if ($currency !== 'NGN') {
            throw new \RuntimeException("Unexpected currency {$currency} for ref: {$txRef}");
        }

        DB::transaction(function () use ($user, $amount, $txRef, $idempotencyKey, $isPermanentVa, $appContext) {
            if ($isPermanentVa) {
                // Always create a fresh transaction row per transfer
                WalletTransaction::create([
                    'user_id'     => $user->id,
                    'type'        => 'credit',
                    'amount'      => $amount,
                    'status'      => 'completed',
                    'description' => 'Wallet top-up via bank transfer',
                    'reference'   => $idempotencyKey,
                ]);
            } else {
                // Card/link: mark the pre-created pending transaction as completed
                $existing = WalletTransaction::where('reference', $idempotencyKey)
                    ->where('status', 'pending')->first();

                if ($existing) {
                    $existing->update(['status' => 'completed', 'amount' => $amount]);
                } else {
                    WalletTransaction::create([
                        'user_id'     => $user->id,
                        'type'        => 'credit',
                        'amount'      => $amount,
                        'status'      => 'completed',
                        'description' => 'Wallet top-up',
                        'reference'   => $idempotencyKey,
                    ]);
                }
            }

            $appContext->updateUserBalance($user->id);
        });

        $notifications->walletTopup((int) $userId, $amount);

        \Illuminate\Support\Facades\Log::info('ProcessWebhook: wallet topped up', [
            'user_id'         => $userId,
            'amount'          => $amount,
            'idempotency_key' => $idempotencyKey,
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
