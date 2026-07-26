<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\ResourceResource;
use App\Models\Resource;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ResourceController extends Controller
{
    /**
     * Display a listing of resources.
     */
    public function index(Request $request)
    {
        $this->authorize('manage-resources');

        $query = Resource::query();

        // Filter by type
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Filter by active status
        if ($request->filled('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        $resources = $query->latest()->paginate(20);

        return Inertia::render('Admin/Resources/Index', [
            'resources' => ResourceResource::collection($resources),
            'filters' => $request->only(['type', 'is_active']),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorize('manage-resources');

        return Inertia::render('Admin/Resources/Create');
    }

    /**
     * Store a newly created resource.
     */
    public function store(Request $request)
    {
        $this->authorize('manage-resources');

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:venue,equipment,vehicle',
            'description' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);

        Resource::create([
            'name' => $validated['name'],
            'type' => $validated['type'],
            'description' => $validated['description'],
            'is_active' => $validated['is_active'] ?? true,
        ]);

        return redirect()->route('admin.resources.index')
            ->with('success', 'Resource created successfully');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Resource $resource)
    {
        $this->authorize('manage-resources');

        return Inertia::render('Admin/Resources/Edit', [
            'resource' => new ResourceResource($resource),
        ]);
    }

    /**
     * Update the specified resource.
     */
    public function update(Request $request, Resource $resource)
    {
        $this->authorize('manage-resources');

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:venue,equipment,vehicle',
            'description' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);

        $resource->update($validated);

        return redirect()->route('admin.resources.index')
            ->with('success', 'Resource updated successfully');
    }

    /**
     * Remove the specified resource.
     */
    public function destroy(Resource $resource)
    {
        $this->authorize('manage-resources');

        // Check if resource is being used
        if ($resource->scheduleEvents()->exists()) {
            return back()->withErrors([
                'resource' => 'Cannot delete resource that is assigned to schedule events. Please remove it from events first or mark as inactive.',
            ]);
        }

        $resource->delete();

        return redirect()->route('admin.resources.index')
            ->with('success', 'Resource deleted successfully');
    }
}
