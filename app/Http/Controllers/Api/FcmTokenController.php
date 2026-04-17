<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UserDevice;
use Illuminate\Http\Request;

class FcmTokenController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'fcm_token' => 'required|string|max:255',
            'device_type' => 'nullable|string',
            'device_name' => 'nullable|string',
        ]);

        UserDevice::updateOrCreate(
            ['fcm_token' => $request->fcm_token],
            [
                'user_id' => $request->user()->id,
                'device_type' => $request->device_type,
                'device_name' => $request->device_name,
                'last_used_at' => now(),
            ]
        );

        return response()->json([
            'message' => 'FCM token registered successfully'
        ]);
    }
}
