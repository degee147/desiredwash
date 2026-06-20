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
     *   "express_multiplier": 1.8,
     *   "standard_order_label": "Standard",
     *   "standard_order_subtitle": "Normal turnaround time",
     *   "express_order_label": "Express",
     *   "express_order_subtitle": "Priority processing",
     *   "express_order_badge": "{multiplier}x price",
     *   "express_order_summary_label": "Express Order ({multiplier}x price)"
     * }
     *
     * Note: "{multiplier}" is a placeholder the client substitutes with the
     * current express_multiplier value (e.g. "1.8"). This keeps copy editable
     * from the admin panel without requiring server-side string formatting
     * per request.
     */
    public function index(): JsonResponse
    {
        return response()->json([
            'phone'                       => Option::get('phone', ''),
            'express_multiplier'          => (float) Option::get('express_multiplier', 1.8),
            'standard_order_label'        => Option::get('standard_order_label', 'Standard'),
            'standard_order_subtitle'     => Option::get('standard_order_subtitle', 'Normal turnaround time'),
            'express_order_label'         => Option::get('express_order_label', 'Express'),
            'express_order_subtitle'      => Option::get('express_order_subtitle', 'Priority processing'),
            'express_order_badge'         => Option::get('express_order_badge', '{multiplier}x price'),
            'express_order_summary_label' => Option::get('express_order_summary_label', 'Express Order ({multiplier}x price)'),
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
