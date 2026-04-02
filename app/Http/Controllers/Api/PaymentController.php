<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\FlutterwaveService;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function __construct(private FlutterwaveService $flutterwave) {}

    // POST /api/v1/payments/verify
    public function verify(Request $request)
    {
        $data = $request->validate([
            'transaction_ref' => 'required|string',
            'order_id'        => 'required|string|exists:orders,id',
        ]);

        $order = $request->user()->orders()->findOrFail($data['order_id']);

        if ($order->payment_status === 'success') {
            return response()->json([
                'success' => true,
                'message' => 'Payment already verified',
                'order'   => $order->toApiArray(),
            ]);
        }

        // Look up transaction by tx_ref to get the numeric Flutterwave transaction ID
        $transaction = $this->flutterwave->getTransactionByRef($data['transaction_ref']);

        if (! $transaction) {
            return response()->json(['message' => 'Transaction not found'], 404);
        }

        // Server-side verification — never trust client
        $verified = $this->flutterwave->verifyTransaction((string) $transaction['id']);

        if (
            ! $verified
            || $verified['status'] !== 'successful'
            || (float) $verified['amount'] < (float) $order->total
            || strtoupper($verified['currency']) !== 'NGN'
        ) {
            return response()->json([
                'success' => false,
                'message' => 'Payment verification failed',
            ], 402);
        }

        $order->update([
            'payment_status'    => 'success',
            'status'            => 'confirmed',
            'payment_reference' => $data['transaction_ref'],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Payment verified successfully',
            'order'   => $order->fresh()->toApiArray(),
        ]);
    }
}
