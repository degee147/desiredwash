<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UserDevice;
use App\Services\NotificationService;
use Illuminate\Http\Request;

class FcmTokenController extends Controller
{
    public function __construct(private NotificationService $notifications)
    {
    }

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

        return response()->json(['message' => 'FCM token registered successfully']);
    }

    // POST /api/v1/fcm/test
    public function sendTest(Request $request)
    {
        $request->validate([
            'user_id' => 'nullable|integer|exists:users,id',
            'title' => 'nullable|string|max:100',
            'body' => 'nullable|string|max:255',
        ]);

        $user = $request->filled('user_id')
            ? \App\Models\User::find($request->user_id)
            : $request->user();

        $devices = UserDevice::where('user_id', $user->id)->count();

        if ($devices === 0) {
            return response()->json(['message' => 'No registered devices for this user'], 422);
        }

        $notification = $this->notifications->send(
            userId: $user->id,
            title: $request->input('title', '🔔 Test Notification'),
            body: $request->input('body', 'Push notifications are working correctly!'),
            type: NotificationService::TYPE_GENERAL,
        );

        return response()->json([
            'message' => 'Test notification sent',
            'notification' => $notification,
            'devices_sent' => $devices,
        ]);
    }
}
