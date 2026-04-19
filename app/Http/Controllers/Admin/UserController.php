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
        return view('admin.users.show', compact('user', 'zone'));
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
                'description' => 'Admin Wallet top-up',
                'reference' => $txRef,
            ]);

            $balance = $this->appContextService->updateUserBalance($user->id);


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
