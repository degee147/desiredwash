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
     * Currently: the admin contact phone number for wallet funding.
     *
     * Response:
     * {
     *   "phone": "2348012345678"
     * }
     */
    public function index(): JsonResponse
    {
        return response()->json([
            'phone' => Option::get('phone', ''),
        ]);
    }

    /**
     * PUT /api/v1/admin/options
     *
     * Admin-only: update any option by key.
     *
     * Body: { "key": "phone", "value": "2348099999999" }
     */
    public function update(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'key' => ['required', 'string', 'max:100'],
            'value' => ['required', 'string', 'max:500'],
        ]);

        Option::set($validated['key'], $validated['value']);

        return response()->json([
            'message' => 'Option updated.',
            'key' => $validated['key'],
            'value' => $validated['value'],
        ]);
    }
}
