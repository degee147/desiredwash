<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'body',
        'type',
        'order_id',
        'is_read',
    ];

    protected $casts = [
        'is_read' => 'boolean',
    ];

    /**
     * Owner of the notification
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Optional related order (deep link)
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Convert to API format matching Flutter AppNotification
     */
    public function toApiArray(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'body' => $this->body,
            'type' => $this->type,
            'created_at' => $this->created_at?->toIso8601String(),
            'is_read' => $this->is_read,
            'order_id' => $this->order_id,
        ];
    }
}
