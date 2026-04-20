<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Zone;
use Illuminate\Http\Request;

class ZoneController extends Controller
{
    public function index()
    {
        $zones = Zone::orderBy('name')->get();

        return view('admin.zones.index', compact('zones'));
    }

    public function create()
    {
        return view('admin.zones.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'id' => 'required|string|max:20|unique:zones,id',
            'name' => 'required|string|max:255',
            'area' => 'required|string|max:255',
            'delivery_fee' => 'required|numeric|min:0',
            'is_available' => 'boolean',
        ]);

        Zone::create($validated);

        return redirect()->route('admin.zones.index')->with('success', 'Zone created.');
    }

    public function show(Zone $zone)
    {
        return redirect()->route('admin.zones.edit', $zone->id);
    }

    public function edit(Zone $zone)
    {
        abort_unless(auth()->user()->isSuperAdmin(), 403);
        return view('admin.zones.edit', compact('zone'));
    }

    public function update(Request $request, Zone $zone)
    {
        $validated = $request->validate([
            'id' => 'required|string|max:20|unique:zones,id,' . $zone->id,
            'name' => 'required|string|max:255',
            'area' => 'required|string|max:255',
            'delivery_fee' => 'required|numeric|min:0',
            'is_available' => 'boolean',
        ]);

        $zone->update($validated);

        return redirect()->route('admin.zones.index')->with('success', 'Zone updated.');
    }

    public function destroy(Zone $zone)
    {
        // if (!auth()->user()->isSuperAdmin()) {
        //     return back()->with('error', 'Only super admins can delete zones.');
        // }
        return back()->with('error', 'Delete is temporarily disabled to prevent accidental deletions.');
        $zone->delete();

        return redirect()->route('admin.zones.index')->with('success', 'Zone deleted.');
    }
}
