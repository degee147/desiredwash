<?php

namespace App\Models;

use App\Models\Notification;
use App\Models\UserDevice;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'sa',
        'admin',
        'support',
        'phone',
        'address',
        'wallet_balance',
        'auth_provider',
        'avatar_url',
        'zone_id',
        'va_bank_name',
        'va_account_number',
        'va_account_name',
        'va_flw_ref',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'wallet_balance' => 'decimal:2',
            'sa' => 'boolean',
            'admin' => 'boolean',
            'support' => 'boolean'
        ];
    }

    public function isSuperAdmin(): bool
    {
        return (bool) $this->sa;
    }

    public function isAdmin(): bool
    {
        return (bool) $this->admin or (bool) $this->sa;
    }

    public function isSupport(): bool
    {
        return (bool) $this->support or (bool) $this->sa or (bool) $this->admin;
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
    public function zone()
    {
        return $this->belongsTo(Zone::class);
    }
    public function walletTransactions()
    {
        return $this->hasMany(WalletTransaction::class);
    }
    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    public function devices()
    {
        return $this->hasMany(UserDevice::class);
    }

    public function toApiArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'address' => $this->address,
            'wallet_balance' => (float) $this->wallet_balance,
            'auth_provider' => $this->auth_provider,
            'zone_id' => $this->zone_id,
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
