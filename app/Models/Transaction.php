<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Transaction extends Model
{
    use HasUuids;

    protected $fillable = [
        'user_id',
        'order_id',
        'tx_ref',
        'flw_tx_id',
        'flw_ref',
        'type',        // order_payment | wallet_topup
        'amount',
        'currency',
        'status',      // pending | successful | failed | cancelled
        'meta',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'meta' => 'array',
    ];

    // ── Relationships ──────────────────────────────────────────

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    // ── Scopes ─────────────────────────────────────────────────

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeSuccessful($query)
    {
        return $query->where('status', 'successful');
    }

    // ── Helpers ────────────────────────────────────────────────

    public function isSuccessful(): bool
    {
        return $this->status === 'successful';
    }

    public function markSuccessful(array $flwData): void
    {
        $this->update([
            'status' => 'successful',
            'flw_tx_id' => $flwData['id'],
            'flw_ref' => $flwData['flw_ref'] ?? null,
            'meta' => $flwData,
        ]);
    }

    public function markFailed(): void
    {
        $this->update(['status' => 'failed']);
    }
}
