<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Option;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function edit()
    {
        abort_unless(auth()->user()->isSuperAdmin(), 403);
        return view('admin.settings.edit', [
            'phone'              => Option::get('phone'),
            'express_multiplier' => Option::get('express_multiplier', 1.8),
        ]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'phone'              => 'required|string|max:20',
            'express_multiplier' => 'required|numeric|min:1|max:10',
        ]);

        Option::set('phone', $request->phone);
        Option::set('express_multiplier', (string) $request->express_multiplier);

        return back()->with('success', 'Settings saved.');
    }
}
