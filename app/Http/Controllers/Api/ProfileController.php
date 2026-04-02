<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    // GET /api/v1/profile
    public function show(Request $request)
    {
        return response()->json(['user' => $request->user()->toApiArray()]);
    }

    // POST /api/v1/profile/update
    public function update(Request $request)
    {
        $data = $request->validate([
            'name'    => 'sometimes|string|max:255',
            'phone'   => 'sometimes|nullable|string|max:20',
            'zone_id' => 'sometimes|nullable|string|exists:zones,id',
        ]);

        $user = $request->user();
        $user->update($data);

        return response()->json(['user' => $user->fresh()->toApiArray()]);
    }
}
