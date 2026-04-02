<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Zone;

class ZoneController extends Controller
{
    // GET /api/v1/zones (public)
    public function index()
    {
        $zones = Zone::all()->map(fn($z) => [
            'id'           => $z->id,
            'name'         => $z->name,
            'area'         => $z->area,
            'is_available' => $z->is_available,
            'delivery_fee' => (float) $z->delivery_fee,
        ]);

        return response()->json(['zones' => $zones]);
    }
}
