<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\WalletTransaction;
use App\Models\Zone;
use App\Services\AppContextService;
use App\Services\FlutterwaveService;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserController extends Controller
{

    public function __construct(
        private FlutterwaveService $flutterwave,
        private NotificationService $notifications,
        private AppContextService $appContextService,
    ) {
    }
    public function index()
    {
        $users = User::latest()->paginate(20);
        return view('admin.users.index', compact('users'));
    }
    public function show(Request $request, User $user)
    {
        $zone = Zone::find($user->zone_id);
        $this->appContextService->updateUserBalance($user->id);

        $user->load([
            'orders',
            'walletTransactions' => fn($q) => $q->latest()->limit(20),
            'transactions'       => fn($q) => $q->latest()->limit(20),
        ]);

        // ── Order stats ───────────────────────────────────────────────────────
        $orders        = $user->orders;
        $orderStats    = [
            'total'     => $orders->count(),
            'confirmed' => $orders->whereIn('status', ['confirmed', 'picked_up', 'washing', 'ready_for_delivery', 'delivered'])->count(),
            'delivered' => $orders->where('status', 'delivered')->count(),
            'cancelled' => $orders->where('status', 'cancelled')->count(),
            'pending'   => $orders->where('status', 'pending')->count(),
            'express'   => $orders->where('order_type', 'express')->count(),
            'total_spent' => $orders->where('payment_status', 'success')->sum('total'),
            'avg_order'   => $orders->where('payment_status', 'success')->avg('total') ?? 0,
            'last_order'  => $orders->sortByDesc('created_at')->first(),
        ];

        // ── Wallet stats ──────────────────────────────────────────────────────
        $walletTxns    = $user->walletTransactions;
        $walletStats   = [
            'balance'       => (float) $user->wallet_balance,
            'total_funded'  => $walletTxns->where('type', 'credit')->where('status', 'completed')->sum('amount'),
            'total_spent'   => $walletTxns->where('type', 'debit')->where('status', 'completed')->sum('amount'),
            'pending_topups'=> $walletTxns->where('type', 'credit')->where('status', 'pending')->count(),
            'has_va'        => (bool) $user->va_account_number,
        ];

        // ── Recent activity (last 8 events across orders + wallet) ────────────
        $recentActivity = collect();

        foreach ($orders->sortByDesc('created_at')->take(5) as $o) {
            $recentActivity->push([
                'type'  => 'order',
                'icon'  => 'fa-shopping-bag',
                'color' => 'primary',
                'label' => 'Order #' . strtoupper(substr($o->id, 0, 8)),
                'sub'   => ucfirst($o->status) . ' · ₦' . number_format((float) $o->total, 0),
                'time'  => $o->created_at,
            ]);
        }

        foreach ($walletTxns->sortByDesc('created_at')->take(5) as $t) {
            $recentActivity->push([
                'type'  => 'wallet',
                'icon'  => $t->type === 'credit' ? 'fa-arrow-down' : 'fa-arrow-up',
                'color' => $t->type === 'credit' ? 'success' : 'danger',
                'label' => $t->description,
                'sub'   => ($t->type === 'credit' ? '+' : '-') . '₦' . number_format((float) $t->amount, 0) . ' · ' . ucfirst($t->status),
                'time'  => $t->created_at,
            ]);
        }

        $recentActivity = $recentActivity->sortByDesc('time')->take(8)->values();

        return view('admin.users.show', compact(
            'user', 'zone', 'orderStats', 'walletStats', 'recentActivity'
        ));
    }




    public function edit(User $user)
    {
        if (!auth()->user()->isSuperAdmin()) {
            abort(403, 'Unauthorized. Superadmin access required.');
        }
        $zones = Zone::orderBy('name')->get();

        return view('admin.users.edit', compact('user', 'zones'));
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'zone_id' => 'nullable|integer',
            'admin' => 'boolean',
            'support' => 'boolean',
        ]);

        $user->update($data);
        $this->appContextService->updateUserBalance($user->id);

        return redirect()->route('admin.users.index')
            ->with('success', 'User updated successfully.');
    }

    public function destroy(User $user)
    {
        return back()->with('error', 'Delete is temporarily disabled to prevent accidental deletions.');
        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'User deleted.');
    }
    public function fundUser(Request $request, User $user)
    {
        // Superadmin only
        if (!auth()->user()->isSuperAdmin()) {
            abort(403, 'Unauthorized. Superadmin access required.');
        }

        $request->validate([
            'amount' => ['required', 'numeric', 'min:1'],
        ]);

        $amount = round((float) $request->amount, 2);

        DB::transaction(function () use ($user, $amount) {

            $timestamp = now()->timestamp;
            $txRef = "wallet_topup_{$user->id}_{$timestamp}";

            WalletTransaction::create([
                'user_id' => $user->id,
                'type' => 'credit',
                'status' => 'completed',
                'amount' => $amount,
                'processed_at' => now(),
                'description' => 'Admin Wallet top-up',
                'reference' => $txRef,
            ]);

            $this->appContextService->updateUserBalance($user->id);


        });


        $this->notifications->walletTopup($user->id, $amount);

        return back()->with('success', '₦' . number_format($amount, 2) . ' added to ' . $user->name . "'s wallet.");
    }

    public function toggleStatus(User $user)
    {
        // We use the 'admin' flag as a proxy for "active" here;
        // swap for a dedicated `is_active` column if you add one.
        $user->update(['admin' => !$user->admin]);

        return back()->with('success', 'User status updated.');
    }

    public function resetPassword(User $user)
    {
        $newPassword = Str::random(10);
        $user->update(['password' => Hash::make($newPassword)]);

        // Optionally email the new password to the user here.

        return back()->with('success', "Password reset. Temporary password: {$newPassword}");
    }
}
