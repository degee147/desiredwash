<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Option;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function edit()
    {
        return view('admin.settings.edit', [
            'phone' => Option::get('phone'),
        ]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'phone' => 'required|string|max:20',
        ]);

        Option::set('phone', $request->phone);

        return back()->with('success', 'Settings saved.');
    }
}
