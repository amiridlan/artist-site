<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\ConflictLogResource;
use App\Models\ConflictLog;
use App\Services\ConflictDetectionService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ConflictLogController extends Controller
{
    public function __construct(
        protected ConflictDetectionService $conflictService
    ) {
    }

    /**
     * Display a listing of conflict logs.
     */
    public function index(Request $request)
    {
        $this->authorize('view-conflict-logs');

        $query = ConflictLog::with(['conflictable', 'resolvedBy']);

        // Filter by resolution status
        if ($request->filled('resolution')) {
            $query->where('resolution', $request->resolution);
        }

        // Filter by conflict type
        if ($request->filled('conflict_type')) {
            $query->where('conflict_type', $request->conflict_type);
        }

        $conflicts = $query->latest()->paginate(20);

        return Inertia::render('Admin/ConflictLogs/Index', [
            'conflicts' => ConflictLogResource::collection($conflicts),
            'filters' => $request->only(['resolution', 'conflict_type']),
            'conflictTypes' => [
                'artist_double_booking' => 'Artist Double Booking',
                'artist_day_off_conflict' => 'Artist Day-Off Conflict',
                'staff_availability' => 'Staff Availability',
                'resource_conflict' => 'Resource Conflict',
            ],
        ]);
    }

    /**
     * Resolve a conflict.
     */
    public function resolve(Request $request, ConflictLog $conflictLog)
    {
        $this->authorize('resolve-conflicts');

        $validated = $request->validate([
            'resolution' => 'required|in:resolved,overridden',
        ]);

        $this->conflictService->resolveConflict(
            $conflictLog,
            $request->user()->id,
            $validated['resolution']
        );

        return back()->with('success', 'Conflict resolved successfully');
    }
}
