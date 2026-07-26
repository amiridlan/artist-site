<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\ScheduleEventResource;
use App\Models\Member;
use App\Models\Resource;
use App\Models\ScheduleEvent;
use App\Models\User;
use App\Services\ConflictDetectionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class ScheduleEventController extends Controller
{
    public function __construct(
        protected ConflictDetectionService $conflictService
    ) {
    }

    /**
     * Display a listing of schedule events.
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', ScheduleEvent::class);

        $query = ScheduleEvent::with(['members', 'staff', 'resources', 'createdBy']);

        // Filter by type
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by date range
        if ($request->filled('start_date')) {
            $query->where('start_datetime', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->where('end_datetime', '<=', $request->end_date);
        }

        // Artists only see their own events
        if ($request->user()->isArtist() && $request->user()->member_id) {
            $query->forMember($request->user()->member_id);
        }

        $events = $query->latest('start_datetime')->paginate(20);

        return Inertia::render('Admin/ScheduleEvents/Index', [
            'events' => ScheduleEventResource::collection($events),
            'filters' => $request->only(['type', 'status', 'start_date', 'end_date']),
        ]);
    }

    /**
     * Show the form for creating a new schedule event.
     */
    public function create(Request $request)
    {
        // Type is required in query string
        $type = $request->query('type');

        if (!$type) {
            return redirect()->route('admin.schedule-events.index')
                ->with('error', 'Event type is required');
        }

        $this->authorize('create', [ScheduleEvent::class, $type]);

        return Inertia::render('Admin/ScheduleEvents/Create', [
            'type' => $type,
            'members' => Member::select('id', 'name_english', 'slug')->get(),
            'staff' => User::whereHas('roles', fn($q) =>
                $q->whereIn('name', ['Marketing Department', 'Events Department'])
            )->select('id', 'name', 'email')->get(),
            'resources' => Resource::active()->select('id', 'name', 'type')->get(),
        ]);
    }

    /**
     * Store a newly created schedule event.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:artist_performance,artist_appearance,content_filming,practice_day,day_off,staff_event,social_media_post',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_datetime' => 'required|date',
            'end_datetime' => 'required|date|after:start_datetime',
            'venue' => 'nullable|string|max:255',
            'status' => 'required|in:draft,confirmed,cancelled',
            'member_ids' => 'nullable|array',
            'member_ids.*' => 'exists:members,id',
            'staff_ids' => 'nullable|array',
            'staff_ids.*' => 'exists:users,id',
            'resource_ids' => 'nullable|array',
            'resource_ids.*' => 'exists:resources,id',
            'override_conflicts' => 'nullable|boolean',
        ]);

        $this->authorize('create', [ScheduleEvent::class, $validated['type']]);

        // Create temporary event for conflict checking
        $tempEvent = new ScheduleEvent($validated);

        // Check for conflicts
        $conflicts = $this->conflictService->checkScheduleEventConflicts(
            $tempEvent,
            $validated['member_ids'] ?? [],
            $validated['staff_ids'] ?? [],
            $validated['resource_ids'] ?? []
        );

        // Block on error-level conflicts unless override is allowed
        $errorConflicts = $conflicts->where('severity', 'error');
        if ($errorConflicts->isNotEmpty() && !$request->boolean('override_conflicts')) {
            // Check if user can override
            if (!$request->user()->can('override-conflicts')) {
                return back()->withErrors([
                    'conflicts' => 'Conflicts detected. You do not have permission to override them.',
                ])->withInput();
            }

            // Return conflicts for user to review
            return back()->with('conflicts', $errorConflicts->toArray())->withInput();
        }

        // Create the event
        DB::transaction(function () use ($validated, $request, $conflicts) {
            $event = ScheduleEvent::create([
                'title' => $validated['title'],
                'description' => $validated['description'],
                'type' => $validated['type'],
                'start_datetime' => $validated['start_datetime'],
                'end_datetime' => $validated['end_datetime'],
                'venue' => $validated['venue'],
                'status' => $validated['status'],
                'created_by' => $request->user()->id,
                'conflict_notes' => $request->boolean('override_conflicts') && $conflicts->isNotEmpty()
                    ? 'Conflicts overridden by ' . $request->user()->name
                    : null,
            ]);

            // Attach relationships
            if (!empty($validated['member_ids'])) {
                $event->members()->attach($validated['member_ids']);
            }
            if (!empty($validated['staff_ids'])) {
                $event->staff()->attach($validated['staff_ids']);
            }
            if (!empty($validated['resource_ids'])) {
                $resourceData = collect($validated['resource_ids'])->mapWithKeys(fn($id) => [$id => ['quantity' => 1]]);
                $event->resources()->attach($resourceData);
            }

            // Log conflicts if overridden
            if ($request->boolean('override_conflicts') && $conflicts->isNotEmpty()) {
                $this->conflictService->logConflicts($event, $conflicts);

                // Mark as overridden immediately
                foreach ($event->conflictLogs as $log) {
                    $this->conflictService->resolveConflict($log, $request->user()->id, 'overridden');
                }
            }
        });

        return redirect()->route('admin.schedule-events.index')
            ->with('success', 'Schedule event created successfully');
    }

    /**
     * Show the form for editing the specified schedule event.
     */
    public function edit(ScheduleEvent $scheduleEvent)
    {
        $this->authorize('update', $scheduleEvent);

        $scheduleEvent->load(['members', 'staff', 'resources', 'conflictLogs.resolvedBy']);

        return Inertia::render('Admin/ScheduleEvents/Edit', [
            'event' => new ScheduleEventResource($scheduleEvent),
            'members' => Member::select('id', 'name_english', 'slug')->get(),
            'staff' => User::whereHas('roles', fn($q) =>
                $q->whereIn('name', ['Marketing Department', 'Events Department'])
            )->select('id', 'name', 'email')->get(),
            'resources' => Resource::active()->select('id', 'name', 'type')->get(),
        ]);
    }

    /**
     * Update the specified schedule event.
     */
    public function update(Request $request, ScheduleEvent $scheduleEvent)
    {
        $this->authorize('update', $scheduleEvent);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_datetime' => 'required|date',
            'end_datetime' => 'required|date|after:start_datetime',
            'venue' => 'nullable|string|max:255',
            'status' => 'required|in:draft,confirmed,cancelled',
            'member_ids' => 'nullable|array',
            'member_ids.*' => 'exists:members,id',
            'staff_ids' => 'nullable|array',
            'staff_ids.*' => 'exists:users,id',
            'resource_ids' => 'nullable|array',
            'resource_ids.*' => 'exists:resources,id',
            'override_conflicts' => 'nullable|boolean',
        ]);

        // Update temp event for conflict checking
        $tempEvent = clone $scheduleEvent;
        $tempEvent->fill($validated);

        // Check for conflicts
        $conflicts = $this->conflictService->checkScheduleEventConflicts(
            $tempEvent,
            $validated['member_ids'] ?? [],
            $validated['staff_ids'] ?? [],
            $validated['resource_ids'] ?? []
        );

        // Block on error-level conflicts unless override is allowed
        $errorConflicts = $conflicts->where('severity', 'error');
        if ($errorConflicts->isNotEmpty() && !$request->boolean('override_conflicts')) {
            if (!$request->user()->can('override-conflicts')) {
                return back()->withErrors([
                    'conflicts' => 'Conflicts detected. You do not have permission to override them.',
                ])->withInput();
            }

            return back()->with('conflicts', $errorConflicts->toArray())->withInput();
        }

        // Update the event
        DB::transaction(function () use ($validated, $request, $scheduleEvent, $conflicts) {
            $scheduleEvent->update([
                'title' => $validated['title'],
                'description' => $validated['description'],
                'start_datetime' => $validated['start_datetime'],
                'end_datetime' => $validated['end_datetime'],
                'venue' => $validated['venue'],
                'status' => $validated['status'],
                'conflict_notes' => $request->boolean('override_conflicts') && $conflicts->isNotEmpty()
                    ? 'Conflicts overridden by ' . $request->user()->name
                    : $scheduleEvent->conflict_notes,
            ]);

            // Sync relationships
            $scheduleEvent->members()->sync($validated['member_ids'] ?? []);
            $scheduleEvent->staff()->sync($validated['staff_ids'] ?? []);

            $resourceData = collect($validated['resource_ids'] ?? [])->mapWithKeys(fn($id) => [$id => ['quantity' => 1]]);
            $scheduleEvent->resources()->sync($resourceData);

            // Log new conflicts if overridden
            if ($request->boolean('override_conflicts') && $conflicts->isNotEmpty()) {
                $this->conflictService->logConflicts($scheduleEvent, $conflicts);

                foreach ($scheduleEvent->fresh()->conflictLogs()->where('resolution', 'pending')->get() as $log) {
                    $this->conflictService->resolveConflict($log, $request->user()->id, 'overridden');
                }
            }
        });

        return redirect()->route('admin.schedule-events.index')
            ->with('success', 'Schedule event updated successfully');
    }

    /**
     * Remove the specified schedule event.
     */
    public function destroy(ScheduleEvent $scheduleEvent)
    {
        $this->authorize('delete', $scheduleEvent);

        $scheduleEvent->delete();

        return redirect()->route('admin.schedule-events.index')
            ->with('success', 'Schedule event deleted successfully');
    }
}
