<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\WalletTransaction;
use App\Services\FlutterwaveService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    public function __construct(private FlutterwaveService $flutterwave) {}

    // POST /api/v1/webhooks/flutterwave
    public function handle(Request $request)
    {
        // Validate webhook secret
        $secret = config('services.flutterwave.webhook_secret');
        if ($secret && $request->header('verif-hash') !== $secret) {
            Log::warning('Flutterwave webhook: invalid hash');
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $payload = $request->all();
        $event   = $payload['event'] ?? null;

        Log::info('Flutterwave webhook received', ['event' => $event]);

        if ($event !== 'charge.completed') {
            return response()->json(['message' => 'Event ignored']);
        }

        $txData = $payload['data'] ?? [];
        $txRef  = $txData['tx_ref'] ?? null;
        $status = $txData['status'] ?? null;

        if ($status !== 'successful' || ! $txRef) {
            return response()->json(['message' => 'Not a successful charge']);
        }

        // Server-side verification
        $verified = $this->flutterwave->verifyTransaction((string) $txData['id']);

        if (! $verified || $verified['status'] !== 'successful') {
            Log::error('Flutterwave webhook: verification failed', ['tx_ref' => $txRef]);
            return response()->json(['message' => 'Verification failed'], 422);
        }

        // Wallet top-up
        if (str_starts_with($txRef, 'wallet_topup_')) {
            $this->processWalletTopup($txRef, (float) $verified['amount']);
            return response()->json(['message' => 'Wallet credited']);
        }

        // Order payment — tx_ref is the order UUID
        $this->processOrderPayment($txRef, (float) $verified['amount']);

        return response()->json(['message' => 'Order confirmed']);
    }

    private function processWalletTopup(string $txRef, float $amount): void
    {
        // Parse user_id from wallet_topup_{user_id}_{timestamp}
        $parts  = explode('_', $txRef);
        $userId = $parts[2] ?? null;

        if (! $userId) {
            Log::error('Webhook: could not parse user_id from wallet topup ref', ['tx_ref' => $txRef]);
            return;
        }

        $alreadyDone = WalletTransaction::where('reference', $txRef)->exists();
        if ($alreadyDone) return;

        DB::transaction(function () use ($userId, $amount, $txRef) {
            \App\Models\User::where('id', $userId)->increment('wallet_balance', $amount);

            WalletTransaction::create([
                'user_id'     => $userId,
                'type'        => 'credit',
                'amount'      => $amount,
                'description' => 'Wallet top-up',
                'reference'   => $txRef,
            ]);
        });
    }

    private function processOrderPayment(string $orderId, float $amount): void
    {
        $order = Order::find($orderId);

        if (! $order) {
            Log::error('Webhook: order not found', ['order_id' => $orderId]);
            return;
        }

        if ($order->payment_status === 'success') return;

        if ($amount < (float) $order->total) {
            Log::error('Webhook: payment amount mismatch', [
                'order_id' => $orderId,
                'expected' => $order->total,
                'received' => $amount,
            ]);
            return;
        }

        $order->update([
            'payment_status'    => 'success',
            'status'            => 'confirmed',
            'payment_reference' => $orderId,
        ]);
    }
}
