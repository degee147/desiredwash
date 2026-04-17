<?php

namespace App\Services;

use App\Models\User;
use App\Traits\CryptoTrait;
use Illuminate\Http\Request;
use App\Traits\TraitsManager;
use App\Traits\Trading\TraderTrait;
use Illuminate\Support\Facades\Auth;


class AppContextService
{
    // use TraitsManager;
    public function getContext(): array
    {
        $user = Auth::user();

        if (!$user) {
            return [];
        }

        // Check using route names (preferred method if you have named routes)
        $currentRoute = request()->route()->getName();


        return [
            'currentUser' => $user,
            'viewCounts' => $this->getViewCounts(),
            'showMetrics' => true,
        ];
    }



    protected function getViewCounts()
    {
        // Your actual logic here
        return [
            'users' => User::count(),
            'topics' => 5,
        ];
    }


    public function handle(Request $request)
    {
        $redirect = $request->input('redirect');
        $user = $request->user();
        $user->update(['last_login_at' => now()]);

        if ($redirect && str_starts_with($redirect, url('/'))) {
            return redirect()->intended($redirect);
        }

        if ($user && $user->autopilot) {
            return redirect()->route('dashboard');
        }


        return redirect()->intended(route('dashboard', absolute: false));
    }
}
