<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Price extends Model
{
    use HasFactory;

    protected $fillable = [
        'item_name',
        'category',
        'service_type',
        'regular_price',
        'express_price',
        'icon_class',
        'description',
        'is_active'
    ];

    protected $casts = [
        'regular_price' => 'decimal:2',
        'express_price' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    public function scopePopular($query)
    {
        return $query->whereIn('category', ['clothing', 'bedding']);
    }

    // Accessors
    public function getFormattedRegularPriceAttribute()
    {
        return number_format((float) $this->regular_price, 2);
    }

    public function getFormattedExpressPriceAttribute()
    {
        return $this->express_price ? number_format((float) $this->express_price, 2) : null;
    }

    // Helper method to get price range display
    public function getPriceRangeAttribute()
    {
        if ($this->express_price) {
            return "₦{$this->formatted_regular_price} - ₦{$this->formatted_express_price}";
        }
        return "₦{$this->formatted_regular_price}";
    }
}
