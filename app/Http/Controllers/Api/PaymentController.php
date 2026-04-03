<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Transaction;
use App\Services\FlutterwaveService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class PaymentController extends Controller
{
    public function __construct(private FlutterwaveService $flutterwave)
    {
    }

    // ── POST /api/v1/payments/initiate ─────────────────────────

    public function initiate(Request $request)
    {
        $data = $request->validate([
            'order_id' => 'required|string|exists:orders,id',
        ]);

        $order = $request->user()
            ->orders()
            ->with('user')
            ->findOrFail($data['order_id']);

        // Don't create a new link if already paid
        if ($order->payment_status === 'success') {
            return response()->json(['message' => 'Order already paid'], 409);
        }

        // Reuse a pending transaction if one exists, avoids duplicate tx_refs
        $transaction = Transaction::where('order_id', $order->id)
            ->where('status', 'pending')
            ->latest()
            ->first();

        if (!$transaction) {
            $txRef = 'DW-' . strtoupper(Str::random(16));

            $transaction = Transaction::create([
                'user_id' => $request->user()->id,
                'order_id' => $order->id,
                'tx_ref' => $txRef,
                'type' => 'order_payment',
                'amount' => $order->total,
                'currency' => 'NGN',
                'status' => 'pending',
            ]);
        }

        $paymentLink = $this->flutterwave->createOrderPaymentLink(
            order: [
                'total' => $order->total,
                'user_email' => $order->user->email,
                'user_name' => $order->user->name,
            ],
            txRef: $transaction->tx_ref,
        );

        if (!$paymentLink) {
            return response()->json(['message' => 'Could not generate payment link'], 502);
        }

        return response()->json([
            'payment_url' => $paymentLink,
            'tx_ref' => $transaction->tx_ref,
        ]);
    }

    // ── POST /api/v1/payments/webhook ──────────────────────────
    // No auth middleware — Flutterwave calls this directly

    public function webhook(Request $request)
    {
        // 1. Verify the request is genuinely from Flutterwave
        $hash = $request->header('verif-hash');
        if ($hash !== config('services.flutterwave.secret_hash')) {
            Log::warning('Flutterwave webhook: invalid hash', ['hash' => $hash]);
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $payload = $request->all();
        $txRef = $payload['data']['tx_ref'] ?? null;
        $status = $payload['data']['status'] ?? null;

        if (!$txRef) {
            return response()->json(['message' => 'Missing tx_ref'], 400);
        }

        $transaction = Transaction::where('tx_ref', $txRef)->first();

        if (!$transaction) {
            Log::warning('Flutterwave webhook: unknown tx_ref', ['tx_ref' => $txRef]);
            // Return 200 so Flutterwave doesn't keep retrying for unknown refs
            return response()->json(['status' => 'ok']);
        }

        // Idempotency — skip if already processed
        if ($transaction->isSuccessful()) {
            return response()->json(['status' => 'ok']);
        }

        if ($status !== 'successful') {
            $transaction->markFailed();
            return response()->json(['status' => 'ok']);
        }

        // 2. Always re-verify server-side, never trust the webhook payload alone
        $flwTxId = $payload['data']['id'] ?? null;
        $verified = $this->flutterwave->verifyTransaction((string) $flwTxId);

        if (
            !$verified
            || $verified['status'] !== 'successful'
            || (float) $verified['amount'] < (float) $transaction->amount
            || strtoupper($verified['currency']) !== $transaction->currency
        ) {
            Log::error('Flutterwave webhook: verification mismatch', [
                'tx_ref' => $txRef,
                'verified' => $verified,
            ]);
            $transaction->markFailed();
            return response()->json(['status' => 'ok']);
        }

        // 3. Update transaction + order atomically
        DB::transaction(function () use ($transaction, $verified) {
            $transaction->markSuccessful($verified);

            if ($transaction->type === 'order_payment' && $transaction->order_id) {
                $transaction->order()->update([
                    'payment_status' => 'success',
                    'status' => 'confirmed',
                    'payment_reference' => $transaction->tx_ref,
                ]);
            }

            if ($transaction->type === 'wallet_topup') {
                $transaction->user->wallet()->increment('balance', $transaction->amount);
            }
        });

        return response()->json(['status' => 'ok']);
    }

    // ── POST /api/v1/payments/verify ───────────────────────────
    // Called by Flutter after WebView redirect as a double-check

    public function verify(Request $request)
    {
        $data = $request->validate([
            'transaction_ref' => 'required|string',
            'order_id' => 'required|string|exists:orders,id',
        ]);

        $order = $request->user()->orders()->findOrFail($data['order_id']);

        if ($order->payment_status === 'success') {
            return response()->json([
                'success' => true,
                'message' => 'Payment already verified',
                'order' => $order->toApiArray(),
            ]);
        }

        $transaction = $this->flutterwave->getTransactionByRef($data['transaction_ref']);

        if (!$transaction) {
            return response()->json(['message' => 'Transaction not found'], 404);
        }

        $verified = $this->flutterwave->verifyTransaction((string) $transaction['id']);

        if (
            !$verified
            || $verified['status'] !== 'successful'
            || (float) $verified['amount'] < (float) $order->total
            || strtoupper($verified['currency']) !== 'NGN'
        ) {
            return response()->json([
                'success' => false,
                'message' => 'Payment verification failed',
            ], 402);
        }

        DB::transaction(function () use ($order, $data, $verified) {
            $order->update([
                'payment_status' => 'success',
                'status' => 'confirmed',
                'payment_reference' => $data['transaction_ref'],
            ]);

            // Sync the transaction record too if it exists
            Transaction::where('tx_ref', $data['transaction_ref'])
                ->pending()
                ->first()
                    ?->markSuccessful($verified);
        });

        return response()->json([
            'success' => true,
            'message' => 'Payment verified successfully',
            'order' => $order->fresh()->toApiArray(),
        ]);
    }
}
