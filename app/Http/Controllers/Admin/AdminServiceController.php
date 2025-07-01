<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Str;

class AdminServiceController extends Controller
{
    public function index(Request $request)
    {
        $query = Service::orderBy('sort_order');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where('name', 'like', "%$search%")
                  ->orWhere('description', 'like', "%$search%");
        }

        if ($request->input('status') === 'inactive') {
            $query->where('is_active', false);
        } elseif ($request->input('status') === 'active') {
            $query->where('is_active', true);
        }

        $services = $query->paginate(10)->withQueryString();
        $totalServices = Service::count();
        $activeServices = Service::where('is_active', true)->count();
        $inactiveServices = Service::where('is_active', false)->count();

        return Inertia::render('Admin/Services', [
            'services' => $services,
            'filters' => $request->only('search', 'status'),
            'totalServices' => $totalServices,
            'activeServices' => $activeServices,
            'inactiveServices' => $inactiveServices,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'icon' => 'nullable|string|max:255',
            'image' => 'nullable|string|max:255',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $validated['slug'] = Str::slug($validated['name']);

        // Set sort_order to the highest + 1 if not provided
        if (!isset($validated['sort_order'])) {
            $validated['sort_order'] = Service::max('sort_order') + 1;
        }

        $service = Service::create($validated);

        return response()->json([
            'message' => 'Service created successfully',
            'service' => $service
        ]);
    }

    public function show(Service $service)
    {
        return response()->json(['service' => $service]);
    }

    public function update(Request $request, Service $service)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'icon' => 'nullable|string|max:255',
            'image' => 'nullable|string|max:255',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $validated['slug'] = Str::slug($validated['name']);

        $service->update($validated);

        return response()->json([
            'message' => 'Service updated successfully',
            'service' => $service->fresh()
        ]);
    }

    public function destroy(Service $service)
    {
        // Check if service has bookings
        $bookingsCount = $service->bookings()->count();

        if ($bookingsCount > 0) {
            return response()->json([
                'error' => 'Cannot delete service with existing bookings. Deactivate it instead.'
            ], 422);
        }

        $service->delete();

        return response()->json(['message' => 'Service deleted successfully']);
    }

    public function toggle(Service $service)
    {
        $service->update(['is_active' => !$service->is_active]);

        return response()->json([
            'message' => 'Service status updated successfully',
            'service' => $service->fresh()
        ]);
    }

    public function updateOrder(Request $request)
    {
        $validated = $request->validate([
            'services' => 'required|array',
            'services.*.id' => 'required|exists:services,id',
            'services.*.sort_order' => 'required|integer|min:0',
        ]);

        foreach ($validated['services'] as $serviceData) {
            Service::where('id', $serviceData['id'])
                   ->update(['sort_order' => $serviceData['sort_order']]);
        }

        return response()->json(['message' => 'Service order updated successfully']);
    }

    public function getActiveServices()
    {
        $services = Service::where('is_active', true)
                          ->orderBy('sort_order')
                          ->get(['id', 'name', 'slug', 'price', 'description']);

        return response()->json(['services' => $services]);
    }
}
