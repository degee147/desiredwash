<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use App\Models\WalletTransaction;
use App\Models\Zone;
use App\Services\AppContextService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;


class DashboardController extends Controller
{


    public function index()
    {
        $today = today();

        // --- Stat cards ---
        $totalOrders = Order::count();
        $totalRevenue = Order::where('payment_status', 'success')->sum('total');
        $paidOrdersCount = Order::where('payment_status', 'success')->count();
        $pendingOrdersCount = Order::where('status', 'pending')->count();
        $totalUsers = User::where('sa', 0)->where('admin', 0)->where('support', 0)->count();
        $totalWalletBalance = User::sum('wallet_balance');

        // --- Today ---
        $ordersToday = Order::whereDate('created_at', $today)->count();
        $revenueToday = Order::whereDate('created_at', $today)->where('payment_status', 'success')->sum('total');
        $newUsersToday = User::whereDate('created_at', $today)->count();
        $cancelledToday = Order::whereDate('updated_at', $today)->where('status', 'cancelled')->count();
        $walletCreditedToday = WalletTransaction::whereDate('created_at', $today)->where('type', 'credit')->where('status', 'completed')->sum('amount');
        $walletTxToday = WalletTransaction::whereDate('created_at', $today)->count();

        // --- Orders by status ---
        $ordersByStatus = Order::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        // --- Recent orders (10) ---
        $recentOrders = Order::with('user')->latest()->take(10)->get();

        // --- Recent wallet transactions (8) ---
        $recentWalletTx = WalletTransaction::with('user')->latest()->take(8)->get();

        // --- Top customers (5) ---
        $topCustomers = User::withCount('orders')
            ->withSum('orders', 'total')
            ->where('sa', 0)->where('admin', 0)->where('support', 0)
            ->having('orders_count', '>', 0)
            ->orderByDesc('orders_sum_total')
            ->take(5)
            ->get();

        // --- Zone activity ---
        $zoneActivity = Zone::withCount('orders')
            ->having('orders_count', '>', 0)
            ->orderByDesc('orders_count')
            ->take(8)
            ->get();

        // --- Recent users (8) ---
        $recentUsers = User::where('sa', 0)->where('admin', 0)->where('support', 0)
            ->latest()
            ->take(8)
            ->get();

        $stats = [
            'total_orders' => $totalOrders,
            'total_revenue' => $totalRevenue,
            'paid_orders_count' => $paidOrdersCount,
            'pending_orders_count' => $pendingOrdersCount,
            'total_users' => $totalUsers,
            'total_wallet_balance' => $totalWalletBalance,
            'orders_today' => $ordersToday,
            'revenue_today' => $revenueToday,
            'new_users_today' => $newUsersToday,
            'cancelled_today' => $cancelledToday,
            'wallet_credited_today' => $walletCreditedToday,
            'wallet_tx_today' => $walletTxToday,
            'orders_by_status' => $ordersByStatus,
        ];

        return view('dashboard', compact(
            'stats',
            'recentOrders',
            'recentWalletTx',
            'topCustomers',
            'zoneActivity',
            'recentUsers',
        ));
    }


    public function profile(Request $request)
    {
        $user = Auth::user();
        return view('dashboard.profile', compact('user'));
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        if ($user->email == "demo@autopilotbot.io" || $user->id == 24) {
            session()->flash('message', "Not available in demo mode");
        } else {
            $user->fill($request->all());
            if ($user->save()) {
                session()->flash('success', 'Your profile has been updated.');
                return redirect()->route('dashboard');
            }
            session()->flash('error', 'Your profile could not be updated. Please, try again.');
        }

        return view('dashboard.profile', compact('user'));
    }
    public function password(Request $request)
    {
        $user = Auth::user();
        // For GET, show the change password form
        return view('dashboard.password', [
            'user' => $user,
        ]);
    }
    public function changePassword(Request $request)
    {
        $user = Auth::user();

        // Block demo user by email or id
        if ($user->email === "demo@autopilotbot.io" || $user->id == 24) {
            return redirect()->back()->with('error', 'Not available in demo mode');
        }

        $request->validate([
            'old_password' => 'required',
            'password1' => 'required|min:8|confirmed',
        ], [
            'password1.confirmed' => 'The password confirmation does not match.',
        ]);

        // Check if old password matches
        if (!Hash::check($request->input('old_password'), $user->password)) {
            return redirect()->back()->with('error', 'Old password is incorrect!');
        }

        // Update password
        $user->password = Hash::make($request->input('password1'));
        $user->save();

        // Notify logic (optional, e.g., event, notification)
        // event(new PasswordChanged($user));

        return redirect()->route('dashboard')->with('success', 'The password is successfully changed');

    }



}
