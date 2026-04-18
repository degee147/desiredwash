<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = Notification::with('user')->latest()->paginate(20);
        return view('admin.notifications.index', compact('notifications'));
    }

    public function create()
    {
        $users = User::orderBy('name')->get(['id', 'name', 'email']);
        return view('admin.notifications.create', compact('users'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'body' => 'required|string',
            'type' => 'required|string|max:50',
            'user_id' => 'nullable|exists:users,id',   // null = broadcast to all
            'order_id' => 'nullable|exists:orders,id',
        ]);

        if (empty($data['user_id'])) {
            // Broadcast to all users
            User::chunk(200, function ($users) use ($data) {
                foreach ($users as $user) {
                    Notification::create(array_merge($data, ['user_id' => $user->id]));
                }
            });
        } else {
            Notification::create($data);
        }

        return redirect()->route('admin.notifications.index')
            ->with('success', 'Notification sent.');
    }

    public function destroy(Notification $notification)
    {
        $notification->delete();
        return back()->with('success', 'Notification deleted.');
    }
}
