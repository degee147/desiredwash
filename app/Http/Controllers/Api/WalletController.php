<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\WalletTransaction;
use App\Services\AppContextService;
use App\Services\FlutterwaveService;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WalletController extends Controller
{
    public function __construct(
        private FlutterwaveService $flutterwave,
        private NotificationService $notifications,
        private AppContextService $appContextService,
    ) {}

    // GET /api/v1/wallet/balance
    public function balance(Request $request)
    {
        $userId  = $request->user()->id;
        $balance = $this->appContextService->updateUserBalance($userId);
        return response()->json(['balance' => (float) $balance]);
    }

    // POST /api/v1/wallet/topup
    public function topup(Request $request)
    {
        $data = $request->validate([
            'amount' => 'required|numeric|min:100',
        ]);

        $user      = $request->user();
        $timestamp = now()->timestamp;
        $txRef     = "wallet_topup_{$user->id}_{$timestamp}";

        // Create PENDING transaction
        WalletTransaction::create([
            'user_id'     => $user->id,
            'type'        => 'credit',
            'status'      => 'pending',
            'amount'      => $data['amount'],
            'description' => 'Wallet top-up',
            'reference'   => $txRef,
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
            'reference'    => $txRef,
        ]);
    }

    // POST /api/v1/wallet/topup/virtual-account
    public function createVirtualAccount(Request $request)
    {
        $data = $request->validate([
            'amount' => 'required|numeric|min:100',
        ]);

        $user      = $request->user();
        $timestamp = now()->timestamp;
        $txRef     = "wallet_va_{$user->id}_{$timestamp}";

        // Create PENDING transaction
        WalletTransaction::create([
            'user_id'     => $user->id,
            'type'        => 'credit',
            'status'      => 'pending',
            'amount'      => $data['amount'],
            'description' => 'Wallet top-up via bank transfer',
            'reference'   => $txRef,
        ]);

        $vaData = $this->flutterwave->createVirtualAccount(
            email:  $user->email,
            name:   $user->name,
            txRef:  $txRef,
            amount: (float) $data['amount'],
            phone:  $user->phone ?? '',
        );

        if (!$vaData) {
            return response()->json([
                'message' => 'Could not generate account details. Please try again.'
            ], 502);
        }

        return response()->json([
            'reference'       => $txRef,
            'bank_name'       => $vaData['bank_name']        ?? null,
            'account_number'  => $vaData['account_number']   ?? null,
            'account_name'    => $vaData['account_name']     ?? $user->name,
            'amount'          => (float) $data['amount'],
            'expires_at'      => $vaData['expiry_date']      ?? null,
            'note'            => 'Transfer the exact amount shown. Your wallet will be credited automatically within minutes.',
        ]);
    }

    // POST /api/v1/wallet/topup/verify
    public function verifyTopup(Request $request)
    {
        $data = $request->validate([
            'transaction_ref' => 'required|string',
        ]);

        $user = $request->user();

        $transaction = WalletTransaction::where('reference', $data['transaction_ref'])
            ->where('user_id', $user->id)
            ->first();

        if (!$transaction) {
            return response()->json(['message' => 'Transaction not found'], 404);
        }

        // Prevent double processing
        if ($transaction->status === 'completed') {
            return response()->json(['new_balance' => (float) $user->wallet_balance]);
        }

        if (config('services.flutterwave.env') === 'staging') {
            $transaction->update(['status' => 'completed']);
            $this->notifications->walletTopup($user->id, (float) $transaction->amount);
            return response()->json(['new_balance' => (float) $user->fresh()->wallet_balance]);
        }

        sleep(10);
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

        $transaction->update(['status' => 'completed']);
        $this->notifications->walletTopup($user->id, $amount);

        return response()->json(['new_balance' => (float) $user->fresh()->wallet_balance]);
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

    public function trunc(Request $request)
    {
        DB::statement('TRUNCATE TABLE transactions');
        DB::statement('TRUNCATE TABLE wallet_transactions');
        DB::statement('TRUNCATE TABLE orders');

        return response()->json(['success' => true]);
    }
}
