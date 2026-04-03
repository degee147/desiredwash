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

        $paymentLink = $this->flutterwave->createWalletTopupLink(
            $user->id,
            (float) $data['amount'],
            $txRef,
            $user->email,
            $user->name
        );

        if (!$paymentLink) {
            return response()->json(['message' => 'Could not initiate payment. Please try again.'], 502);
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

        // Prevent double-crediting
        $alreadyProcessed = WalletTransaction::where('reference', $data['transaction_ref'])
            ->where('user_id', $user->id)
            ->exists();

        if ($alreadyProcessed) {
            return response()->json(['new_balance' => (float) $user->wallet_balance]);
        }

        // Look up and verify the transaction
        $transaction = $this->flutterwave->getTransactionByRef($data['transaction_ref']);

        if (!$transaction) {
            return response()->json(['message' => 'Transaction not found'], 404);
        }

        $verified = $this->flutterwave->verifyTransaction((string) $transaction['id']);

        if (
            !$verified
            || $verified['status'] !== 'successful'
            || strtoupper($verified['currency']) !== 'NGN'
        ) {
            return response()->json(['message' => 'Payment verification failed'], 402);
        }

        $amount = (float) $verified['amount'];

        $newBalance = DB::transaction(function () use ($user, $amount, $data) {
            $user->increment('wallet_balance', $amount);

            WalletTransaction::create([
                'user_id' => $user->id,
                'type' => 'credit',
                'amount' => $amount,
                'description' => 'Wallet top-up',
                'reference' => $data['transaction_ref'],
            ]);

            return (float) $user->fresh()->wallet_balance;
        });

        return response()->json(['new_balance' => $newBalance]);
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
