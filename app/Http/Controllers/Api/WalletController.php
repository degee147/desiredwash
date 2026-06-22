<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
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

    // ── GET /api/v1/wallet/balance ────────────────────────────────────────────

    public function balance(Request $request)
    {
        $userId  = $request->user()->id;
        $balance = $this->appContextService->updateUserBalance($userId);
        return response()->json(['balance' => (float) $balance]);
    }

    // ── GET /api/v1/wallet/virtual-account ───────────────────────────────────
    //
    // Returns the user's permanent virtual account, creating one on Flutterwave
    // if the user doesn't have one yet.
    // Any bank transfer to this account credits the wallet via webhook.

    public function virtualAccount(Request $request)
    {
        $user = $request->user();

        // Return cached account if already provisioned
        if ($user->va_account_number) {
            return response()->json($this->formatVaResponse($user));
        }

        // Provision a new permanent virtual account
        $txRef  = "wallet_va_{$user->id}";
        $vaData = $this->flutterwave->createVirtualAccount(
            email: $user->email,
            name:  $user->name,
            txRef: $txRef,
            phone: $user->phone ?? '',
        );

        if (!$vaData) {
            return response()->json([
                'message' => 'Could not generate account details. Please try again.',
            ], 502);
        }

        // Persist on user so we never call Flutterwave again for this user
        $user->update([
            'va_bank_name'      => $vaData['bank_name']      ?? null,
            'va_account_number' => $vaData['account_number'] ?? null,
            'va_account_name'   => $vaData['account_name']   ?? $user->name,
            'va_flw_ref'        => $vaData['order_ref']      ?? $txRef,
        ]);

        return response()->json($this->formatVaResponse($user->fresh()));
    }

    private function formatVaResponse($user): array
    {
        return [
            'bank_name'      => $user->va_bank_name,
            'account_number' => $user->va_account_number,
            'account_name'   => $user->va_account_name,
            'note'           => 'Transfer any amount to this account. Your wallet will be credited automatically.',
        ];
    }

    // ── POST /api/v1/wallet/topup ─────────────────────────────────────────────
    // Card/link top-up flow (unchanged)

    public function topup(Request $request)
    {
        $data = $request->validate([
            'amount' => 'required|numeric|min:100',
        ]);

        $user      = $request->user();
        $timestamp = now()->timestamp;
        $txRef     = "wallet_topup_{$user->id}_{$timestamp}";

        WalletTransaction::create([
            'user_id'     => $user->id,
            'type'        => 'credit',
            'status'      => 'pending',
            'amount'      => $data['amount'],
            'description' => 'Wallet top-up',
            'reference'   => $txRef,
        ]);

        $paymentLink = $this->flutterwave->createWalletTopupLink(
            $user->id,
            (float) $data['amount'],
            $txRef,
            $user->email,
            $user->name
        );

        if (!$paymentLink) {
            return response()->json([
                'message' => 'Could not initiate payment. Please try again.',
            ], 502);
        }

        return response()->json([
            'payment_link' => $paymentLink,
            'reference'    => $txRef,
        ]);
    }

    // ── POST /api/v1/wallet/topup/verify ─────────────────────────────────────
    // Card payment verification (unchanged — bank transfers credit via webhook)

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

        if (!$verified || $verified['status'] !== 'successful' || strtoupper($verified['currency']) !== 'NGN') {
            return response()->json(['message' => 'Payment verification failed'], 402);
        }

        $amount = (float) $verified['amount'];
        $transaction->update(['status' => 'completed']);
        $this->notifications->walletTopup($user->id, $amount);

        return response()->json(['new_balance' => (float) $user->fresh()->wallet_balance]);
    }

    // ── GET /api/v1/wallet/transactions ──────────────────────────────────────

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
