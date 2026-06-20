<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Option;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OptionsController extends Controller
{
    /**
     * GET /api/v1/options
     *
     * Returns public app-wide settings.
     *
     * Response:
     * {
     *   "phone": "2348012345678",
     *   "express_multiplier": 1.8
     * }
     */
    public function index(): JsonResponse
    {
        return response()->json([
            'phone'              => Option::get('phone', ''),
            'express_multiplier' => (float) Option::get('express_multiplier', 1.8),
        ]);
    }

    /**
     * PUT /api/v1/admin/options
     *
     * Admin-only: update any option by key.
     */
    public function update(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'key'   => ['required', 'string', 'max:100'],
            'value' => ['required', 'string', 'max:500'],
        ]);

        Option::set($validated['key'], $validated['value']);

        return response()->json([
            'message' => 'Option updated.',
            'key'     => $validated['key'],
            'value'   => $validated['value'],
        ]);
    }
}
