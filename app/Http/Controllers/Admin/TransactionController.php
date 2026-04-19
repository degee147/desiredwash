<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\WalletTransaction;
use App\Services\AppContextService;
use App\Services\FlutterwaveService;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    public function __construct(
        private NotificationService $notifications,
        private AppContextService $appContextService,
    ) {
    }

    public function index()
    {
        $transactions = Transaction::with('user', 'order')->latest()->paginate(20);
        return view('admin.transactions.index', compact('transactions'));
    }

    public function show(Transaction $transaction)
    {
        $transaction->load('user', 'order');
        return view('admin.transactions.show', compact('transaction'));
    }

    public function verify(Request $request, Transaction $transaction, FlutterwaveService $flw)
    {
        if ($transaction->isSuccessful()) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['message' => 'Transaction is already marked as successful.'], 422);
            }
            return back()->with('error', 'Transaction is already marked as successful.');
        }

        // Use existing flw_tx_id if available, otherwise query by tx_ref
        $flwTxId = $transaction->flw_tx_id;

        if (!$flwTxId) {
            $flwTx = $flw->getTransactionByRef($transaction->tx_ref);
            $flwTxId = $flwTx['id'] ?? null;
        }

        if (!$flwTxId) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['message' => 'Transaction not found on Flutterwave.'], 404);
            }
            return back()->with('error', 'Transaction not found on Flutterwave.');
        }

        $data = $flw->verifyTransaction((string) $flwTxId);

        if (!$data) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['message' => 'Flutterwave verification failed. Check logs for details.'], 502);
            }
            return back()->with('error', 'Flutterwave verification failed. Check logs for details.');
        }

        $flwStatus = $data['status'] ?? null;

        if ($flwStatus !== 'successful') {
            if ($flwStatus === 'failed') {
                $transaction->markFailed();
            }

            $message = "Flutterwave reports status: {$flwStatus}."
                . ($flwStatus === 'failed' ? ' Transaction marked as failed.' : ' No changes made.');

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['message' => $message, 'flw_status' => $flwStatus], 422);
            }
            return back()->with('error', $message);
        }

        // Amount sanity check
        if ((float) $data['amount'] < (float) $transaction->amount) {
            Log::warning('Flutterwave amount mismatch on verify', [
                'transaction_id' => $transaction->id,
                'expected' => $transaction->amount,
                'received' => $data['amount'],
            ]);

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['message' => 'Amount mismatch between local record and Flutterwave.'], 422);
            }
            return back()->with('error', 'Amount mismatch between local record and Flutterwave.');
        }

        DB::transaction(function () use ($transaction, $data) {
            $transaction->markSuccessful($data);

            if ($transaction->type === 'wallet_topup') {
                $existing = WalletTransaction::where('reference', $transaction->tx_ref)->first();

                if (!$existing) {
                    WalletTransaction::create([
                        'user_id' => $transaction->user_id,
                        'type' => 'credit',
                        'status' => 'completed',
                        'amount' => $transaction->amount,
                        'description' => 'Wallet top-up (admin verified)',
                        'reference' => $transaction->tx_ref,
                    ]);

                    $this->appContextService->updateUserBalance($transaction->user_id);
                } elseif ($existing->status !== 'completed') {
                    $existing->update(['status' => 'completed']);
                }
            }

            if ($transaction->type === 'order_payment' && $transaction->order_id) {
                $transaction->order()->update(['payment_status' => 'paid']);
            }
        });

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'message' => 'Transaction verified and updated successfully.',
                'flw_status' => $flwStatus,
            ]);
        }

        return back()->with('success', 'Transaction verified and updated successfully.');
    }
}
