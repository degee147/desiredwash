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
            'phone'                       => Option::get('phone'),
            'express_multiplier'          => Option::get('express_multiplier', 1.8),
            'standard_order_label'        => Option::get('standard_order_label', 'Standard'),
            'standard_order_subtitle'     => Option::get('standard_order_subtitle', 'Normal turnaround time'),
            'express_order_label'         => Option::get('express_order_label', 'Express'),
            'express_order_subtitle'      => Option::get('express_order_subtitle', 'Priority processing'),
            'express_order_badge'         => Option::get('express_order_badge', '{multiplier}x price'),
            'express_order_summary_label' => Option::get('express_order_summary_label', 'Express Order ({multiplier}x price)'),
        ]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'phone'                       => 'required|string|max:20',
            'express_multiplier'          => 'required|numeric|min:1|max:10',
            'standard_order_label'        => 'required|string|max:50',
            'standard_order_subtitle'     => 'required|string|max:100',
            'express_order_label'         => 'required|string|max:50',
            'express_order_subtitle'      => 'required|string|max:100',
            'express_order_badge'         => 'required|string|max:100',
            'express_order_summary_label' => 'required|string|max:150',
        ]);

        Option::set('phone', $request->phone);
        Option::set('express_multiplier', (string) $request->express_multiplier);
        Option::set('standard_order_label', $request->standard_order_label);
        Option::set('standard_order_subtitle', $request->standard_order_subtitle);
        Option::set('express_order_label', $request->express_order_label);
        Option::set('express_order_subtitle', $request->express_order_subtitle);
        Option::set('express_order_badge', $request->express_order_badge);
        Option::set('express_order_summary_label', $request->express_order_summary_label);

        return back()->with('success', 'Settings saved.');
    }
}
