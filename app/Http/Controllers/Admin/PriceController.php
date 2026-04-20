<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Price;
use Illuminate\Http\Request;

class PriceController extends Controller
{
    public function index()
    {
        $prices = Price::orderBy('category')->orderBy('item_name')->paginate(30);
        return view('admin.prices.index', compact('prices'));
    }

    public function create()
    {
        return view('admin.prices.create');
    }

    public function store(Request $request)
    {
        $data = $this->validatedData($request);
        Price::create($data);

        return redirect()->route('admin.prices.index')
            ->with('success', 'Price created.');
    }

    public function edit(Price $price)
    {
        abort_unless(auth()->user()->isSuperAdmin(), 403);
        return view('admin.prices.edit', compact('price'));
    }

    public function update(Request $request, Price $price)
    {
        $price->update($this->validatedData($request));

        return redirect()->route('admin.prices.index')
            ->with('success', 'Price updated.');
    }

    public function destroy(Price $price)
    {
        return back()->with('error', 'Delete is temporarily disabled to prevent accidental deletions.');
        // dd(auth()->user()->isSuperAdmin());
        // abort_unless(auth()->user()->isSuperAdmin(), 403);
        $price->delete();
        return back()->with('success', 'Price deleted.');
    }

    private function validatedData(Request $request): array
    {
        return $request->validate([
            'item_name' => 'required|string|max:255',
            'category' => 'required|string|max:100',
            'service_type' => 'nullable|string|max:100',
            'regular_price' => 'required|numeric|min:0',
            'express_price' => 'nullable|numeric|min:0',
            'icon_class' => 'nullable|string|max:100',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);
    }
}
