<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\WalletTransaction;
use App\Services\FlutterwaveService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WalletController extends Controller
{
    public function __construct(private FlutterwaveService $flutterwave)
    {
    }

    // GET /api/v1/wallet/balance
    public function balance(Request $request)
    {
        return response()->json([
            'balance' => (float) $request->user()->wallet_balance,
        ]);
    }

    // POST /api/v1/wallet/topup
    public function topup(Request $request)
    {
        $data = $request->validate([
            'amount' => 'required|numeric|min:100',
        ]);

        $user = $request->user();

        $timestamp = now()->timestamp;
        $txRef = "wallet_topup_{$user->id}_{$timestamp}";

        // ✅ Create PENDING transaction
        WalletTransaction::create([
            'user_id' => $user->id,
            'type' => 'credit',
            'status' => 'pending',
            'amount' => $data['amount'],
            'description' => 'Wallet top-up',
            'reference' => $txRef,
        ]);

        // Generate payment link
        $paymentLink = $this->flutterwave->createWalletTopupLink(
            $user->id,
            (float) $data['amount'],
            $txRef,
            $user->email,
            $user->name
        );

        if (!$paymentLink) {
            return response()->json([
                'message' => 'Could not initiate payment. Please try again.'
            ], 502);
        }

        return response()->json([
            'payment_link' => $paymentLink,
            'reference' => $txRef,
        ]);
    }

    // POST /api/v1/wallet/topup/verify
    public function verifyTopup(Request $request)
    {
        $data = $request->validate([
            'transaction_ref' => 'required|string',
        ]);

        $user = $request->user();

        // Find transaction
        $transaction = WalletTransaction::where('reference', $data['transaction_ref'])
            ->where('user_id', $user->id)
            ->first();


        if (!$transaction) {
            return response()->json(['message' => 'Transaction not found'], 404);
        }

        // Prevent double processing
        if ($transaction->status === 'completed') {
            return response()->json([
                'new_balance' => (float) $user->wallet_balance
            ]);
        }


        if (config('services.flutterwave.env') === 'staging') {

            // Update transaction → triggers model event
            $transaction->update([
                'status' => 'completed',
            ]);

            return response()->json([
                'new_balance' => (float) $user->fresh()->wallet_balance
            ]);


        }


        sleep(10); // wait for FW to update transaction status
        // Get transaction from Flutterwave
        $fwTransaction = $this->flutterwave->getTransactionByRef($data['transaction_ref']);

        if (!$fwTransaction) {
            return response()->json(['message' => 'FW Transaction not found'], 404);
        }

        $verified = $this->flutterwave->verifyTransaction((string) $fwTransaction['id']);


        if (
            !$verified ||
            $verified['status'] !== 'successful' ||
            strtoupper($verified['currency']) !== 'NGN'
        ) {
            return response()->json(['message' => 'Payment verification failed'], 402);
        }

        $amount = (float) $verified['amount'];


        // Update transaction → triggers model event
        $transaction->update([
            'status' => 'completed',
        ]);

        return response()->json([
            'new_balance' => (float) $user->fresh()->wallet_balance
        ]);


    }

    // GET /api/v1/wallet/transactions
    public function transactions(Request $request)
    {
        $transactions = $request->user()
            ->walletTransactions()
            ->latest()
            ->get()
            ->map(fn($t) => $t->toApiArray());

        return response()->json(['transactions' => $transactions]);
    }
}
