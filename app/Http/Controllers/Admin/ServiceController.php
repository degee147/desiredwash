<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function index()
    {
        $services = Service::orderBy('name')->paginate(20);
        return view('admin.services.index', compact('services'));
    }

    public function create()
    {
        return view('admin.services.create');
    }

    public function store(Request $request)
    {
        Service::create($this->validatedData($request));

        return redirect()->route('admin.services.index')
            ->with('success', 'Service created.');
    }

    public function edit(Service $service)
    {
        return view('admin.services.edit', compact('service'));
    }

    public function update(Request $request, Service $service)
    {
        $service->update($this->validatedData($request));

        return redirect()->route('admin.services.index')
            ->with('success', 'Service updated.');
    }

    public function destroy(Service $service)
    {
        $service->delete();
        return back()->with('success', 'Service deleted.');
    }

    private function validatedData(Request $request): array
    {
        return $request->validate([
            'name' => 'required|string|max:255',
            'emoji' => 'nullable|string|max:10',
            'price' => 'required|numeric|min:0',
        ]);
    }
}
