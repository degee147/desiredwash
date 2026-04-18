<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Package;
use Illuminate\Http\Request;

class PackageController extends Controller
{
    public function index()
    {
        $packages = Package::ordered()->paginate(20);
        return view('admin.packages.index', compact('packages'));
    }

    public function create()
    {
        return view('admin.packages.create');
    }

    public function store(Request $request)
    {
        Package::create($this->validatedData($request));

        return redirect()->route('admin.packages.index')
            ->with('success', 'Package created.');
    }

    public function edit(Package $package)
    {
        return view('admin.packages.edit', compact('package'));
    }

    public function update(Request $request, Package $package)
    {
        $package->update($this->validatedData($request));

        return redirect()->route('admin.packages.index')
            ->with('success', 'Package updated.');
    }

    public function destroy(Package $package)
    {
        $package->delete();
        return back()->with('success', 'Package deleted.');
    }

    private function validatedData(Request $request): array
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'price' => 'required|numeric|min:0',
            'old_price' => 'nullable|numeric|min:0',
            'items' => 'nullable|array',
            'items.*' => 'string',
            'icon_class' => 'nullable|string|max:100',
            'sort_order' => 'integer|min:0',
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
        ]);

        // Ensure booleans default to false when unchecked
        $data['is_featured'] = $request->boolean('is_featured');
        $data['is_active'] = $request->boolean('is_active');

        return $data;
    }
}
