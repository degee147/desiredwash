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
        'type',
        'amount',
        'description',
        'reference',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function toApiArray(): array
    {
        return [
            'id'          => $this->id,
            'user_id'     => $this->user_id,
            'type'        => $this->type,
            'amount'      => (float) $this->amount,
            'description' => $this->description,
            'reference'   => $this->reference,
            'created_at'  => $this->created_at?->toIso8601String(),
        ];
    }
}
