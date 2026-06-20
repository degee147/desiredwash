<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasUuids;

    protected $fillable = [
        'id',
        'user_id',
        'zone_id',
        'zone_name',
        'address',
        'items',
        'scheduled_pickup_date',
        'scheduled_pickup_time',
        'subtotal',
        'delivery_fee',
        'total',
        'payment_method',
        'payment_status',
        'status',
        'notes',
        'payment_reference',
    ];

    protected $casts = [
        'items'                 => 'array',
        'subtotal'              => 'decimal:2',
        'delivery_fee'          => 'decimal:2',
        'total'                 => 'decimal:2',
        'scheduled_pickup_date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function toApiArray(): array
    {
        return [
            'id'                    => $this->id,
            'user_id'               => $this->user_id,
            'zone_id'               => $this->zone_id,
            'zone_name'             => $this->zone_name,
            'address'               => $this->address,
            'items'                 => $this->items,
            'scheduled_pickup_date' => $this->scheduled_pickup_date?->toDateString(),
            'scheduled_pickup_time' => $this->scheduled_pickup_time,
            'subtotal'              => (float) $this->subtotal,
            'delivery_fee'          => (float) $this->delivery_fee,
            'total'                 => (float) $this->total,
            'payment_method'        => $this->payment_method,
            'payment_status'        => $this->payment_status,
            'status'                => $this->status,
            'order_type'            => $this->order_type ?? 'standard',
            'notes'                 => $this->notes,
            'created_at'            => $this->created_at?->toIso8601String(),
        ];
    }
}
