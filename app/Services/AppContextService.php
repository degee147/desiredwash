<?php

namespace App\Services;

use App\Models\User;
use App\Models\WalletTransaction;
use App\Traits\CryptoTrait;
use App\Traits\Trading\TraderTrait;
use App\Traits\TraitsManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


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
        // $user->update(['last_login_at' => now()]);

        if ($redirect && str_starts_with($redirect, url('/'))) {
            return redirect()->intended($redirect);
        }


        return redirect()->intended(route('dashboard', absolute: false));
    }

    public function updateUserBalance($userId)
    {
        return DB::transaction(function () use ($userId) {

            $balance = WalletTransaction::where('user_id', $userId)
                ->where('status', 'completed')
                ->sum(DB::raw("
            CASE
                WHEN type = 'credit' THEN amount
                WHEN type = 'debit' THEN -amount
                ELSE 0
            END
        "));

            $user = User::where('id', $userId)
                ->lockForUpdate()
                ->first();

            $user->wallet_balance = $balance;
            $user->save();

            return $balance;
        });
    }
}
