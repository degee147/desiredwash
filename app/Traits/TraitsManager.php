<?php

namespace App\Traits;

use App\Traits\TimeTrait;
use App\Traits\UtilsTrait;



trait TraitsManager
{
    private const STATUS_FLOW = [
        'pending' => 'confirmed',
        'confirmed' => 'picked_up',
        'picked_up' => 'processing',
        'processing' => 'ready',
        'ready' => 'delivered',
    ];
    public $currentUser;


    use UtilsTrait, TimeTrait;

    // IndicatorTrait;
    private function badge(string $value, string $context = 'order'): string
    {
        $map = match ($context) {
            'payment' => [
                'successful' => 'badge-success',
                'pending' => 'badge-warning',
                'failed' => 'badge-danger',
                'cancelled' => 'badge-secondary',
            ],
            'order_type' => [
                'express' => 'badge-warning',
                'standard' => 'badge-light',
            ],
            default => [
                'delivered' => 'badge-success',
                'processing' => 'badge-info',
                'picked_up' => 'badge-info',
                'confirmed' => 'badge-primary',
                'ready' => 'badge-primary',
                'pending' => 'badge-warning',
                'cancelled' => 'badge-danger',
            ],
        };

        $icon = $context === 'order_type' && $value === 'express' ? '⚡ ' : '';

        return '<span class="badge ' . ($map[$value] ?? 'badge-secondary') . '">'
            . $icon . e(ucfirst(str_replace('_', ' ', $value))) . '</span>';
    }
    // private function badge(string $value, string $type = 'status'): string
    // {
    //     $maps = [
    //         'status' => [
    //             'pending' => 'secondary',
    //             'confirmed' => 'info',
    //             'picked_up' => 'info',
    //             'processing' => 'warning',
    //             'ready' => 'primary',
    //             'delivered' => 'success',
    //             'cancelled' => 'danger',
    //         ],
    //         'payment' => [
    //             'unpaid' => 'danger',
    //             'pending' => 'warning',
    //             'paid' => 'success',
    //             'refunded' => 'secondary',
    //             'failed' => 'danger',
    //         ],
    //     ];

    //     $colour = $maps[$type][$value] ?? 'secondary';
    //     $label = ucfirst(str_replace('_', ' ', $value));

    //     return '<span class="badge badge-' . $colour . '">' . e($label) . '</span>';
    // }


}
