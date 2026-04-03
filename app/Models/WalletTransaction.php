<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class WalletTransaction extends Model
{
    use HasUuids;

    protected $fillable = [
        'id',
        'user_id',
        'type',        // credit | debit
        'status',      // pending | completed | failed
        'amount',
        'description',
        'reference',
        'processed_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'processed_at' => 'datetime',
    ];

    protected static function booted()
    {
        static::updated(function ($transaction) {

            // Only process when status changes to completed
            if (
                $transaction->wasChanged('status') &&
                $transaction->status === 'completed'
            ) {
                $user = $transaction->user()->lockForUpdate()->first();

                if (!$user) {
                    throw new \Exception('Wallet transaction user not found');
                }

                // Prevent double processing
                if ($transaction->processed_at) {
                    return;
                }

                if ($transaction->type === 'debit') {
                    if ((float) $user->wallet_balance < (float) $transaction->amount) {
                        throw new \Exception('Insufficient wallet balance');
                    }

                    $user->decrement('wallet_balance', $transaction->amount);
                }

                if ($transaction->type === 'credit') {
                    $user->increment('wallet_balance', $transaction->amount);
                }

                $transaction->processed_at = now();
                $transaction->saveQuietly();
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function toApiArray(): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'type' => $this->type,
            'status' => $this->status,
            'amount' => (float) $this->amount,
            'description' => $this->description,
            'reference' => $this->reference,
            'processed_at' => $this->processed_at?->toIso8601String(),
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
