<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\AppContextService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;


class DashboardController extends Controller
{

    protected $appContextService;

    public function __construct(AppContextService $appContextService)
    {
        $this->appContextService = $appContextService;
    }

    public function index(Request $request)
    {
        $request = request();
        // --- Date Range Setup ---
        $label = 'Last 3 Months';
        $startDate = Carbon::now()->subMonths(3)->startOfMonth()->format('Y-m-d H:i:s');
        $endDate = Carbon::now()->format('Y-m-d H:i:s');

        if ($request->isMethod('post')) {
            $startDate = $request->input('startDate');
            $endDate = $request->input('endDate');
            $label = $request->input('label');
        }

        // --- Bot ---

        // --- Today's Wins & Losses ---
        $todayStart = Carbon::today()->format('Y-m-d H:i:s');
        $now = Carbon::now()->format('Y-m-d H:i:s');

        return view('dashboard', compact('label', 'startDate', 'endDate'))->with($this->appContextService->getContext());
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
