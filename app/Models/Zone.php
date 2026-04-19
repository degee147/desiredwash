<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Zone extends Model
{
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'name',
        'area',
        'is_available',
        'delivery_fee',
    ];

    protected $casts = [
        'is_available' => 'boolean',
        'delivery_fee' => 'decimal:2',
    ];

    public function orders()
    {
        return $this->hasMany(Order::class, 'zone_id');
    }
}
