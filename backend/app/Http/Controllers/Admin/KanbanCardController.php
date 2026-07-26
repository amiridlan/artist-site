<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\KanbanCardResource;
use App\Models\KanbanCard;
use App\Models\Member;
use App\Models\Resource;
use App\Models\ScheduleEvent;
use App\Models\User;
use App\Services\ConflictDetectionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class KanbanCardController extends Controller
{
    public function __construct(
        protected ConflictDetectionService $conflictService
    ) {
    }

    /**
     * Display the kanban board.
     */
    public function index()
    {
        $this->authorize('viewAny', KanbanCard::class);

        $cards = KanbanCard::with(['members', 'staff', 'createdBy', 'scheduleEvent'])
            ->orderBy('stage')
            ->orderBy('position')
            ->get()
            ->groupBy('stage');

        // Ensure all stages exist in the response
        $stages = ['backlog', 'planning', 'confirmed', 'completed'];
        $groupedCards = collect($stages)->mapWithKeys(function ($stage) use ($cards) {
            return [$stage => KanbanCardResource::collection($cards->get($stage, collect()))->resolve()];
        });

        return Inertia::render('Admin/Kanban/Index', [
            'cards' => $groupedCards,
            'stages' => $stages,
        ]);
    }

    /**
     * Store a new kanban card.
     */
    public function store(Request $request)
    {
        $this->authorize('create', KanbanCard::class);

        $validated = $request->validate([
            'type' => 'required|in:artist_performance,artist_appearance,content_filming,practice_day,day_off,staff_event,social_media_post',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'stage' => 'required|in:backlog,planning,confirmed,completed',
            'due_date' => 'nullable|date',
            'member_ids' => 'nullable|array',
            'member_ids.*' => 'exists:members,id',
            'staff_ids' => 'nullable|array',
            'staff_ids.*' => 'exists:users,id',
        ]);

        DB::transaction(function () use ($validated, $request) {
            // Get the max position for the stage
            $maxPosition = KanbanCard::where('stage', $validated['stage'])->max('position') ?? 0;

            $card = KanbanCard::create([
                'title' => $validated['title'],
                'description' => $validated['description'] ?? null,
                'type' => $validated['type'],
                'stage' => $validated['stage'],
                'due_date' => $validated['due_date'] ?? null,
                'position' => $maxPosition + 1,
                'created_by' => $request->user()->id,
            ]);

            // Attach relationships
            if (!empty($validated['member_ids'])) {
                $card->members()->attach($validated['member_ids']);
            }
            if (!empty($validated['staff_ids'])) {
                $card->staff()->attach($validated['staff_ids']);
            }
        });

        return redirect()->route('admin.kanban.index')
            ->with('success', 'Kanban card created successfully');
    }

    /**
     * Update a kanban card.
     */
    public function update(Request $request, KanbanCard $kanbanCard)
    {
        $this->authorize('update', $kanbanCard);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'nullable|date',
            'member_ids' => 'nullable|array',
            'member_ids.*' => 'exists:members,id',
            'staff_ids' => 'nullable|array',
            'staff_ids.*' => 'exists:users,id',
        ]);

        DB::transaction(function () use ($validated, $kanbanCard) {
            $kanbanCard->update([
                'title' => $validated['title'],
                'description' => $validated['description'] ?? null,
                'due_date' => $validated['due_date'] ?? null,
            ]);

            $kanbanCard->members()->sync($validated['member_ids'] ?? []);
            $kanbanCard->staff()->sync($validated['staff_ids'] ?? []);
        });

        return redirect()->route('admin.kanban.index')
            ->with('success', 'Kanban card updated successfully');
    }

    /**
     * Move a card between stages or reorder within a stage.
     */
    public function move(Request $request, KanbanCard $kanbanCard)
    {
        $this->authorize('update', $kanbanCard);

        $validated = $request->validate([
            'stage' => 'required|in:backlog,planning,confirmed,completed',
            'position' => 'required|integer|min:0',
        ]);

        DB::transaction(function () use ($validated, $kanbanCard) {
            $oldStage = $kanbanCard->stage;
            $newStage = $validated['stage'];
            $newPosition = $validated['position'];

            // Moving to confirmed requires special handling (creates schedule event)
            if ($newStage === 'confirmed' && $oldStage !== 'confirmed') {
                // This should be done via confirm() method, not move()
                abort(422, 'Use the confirm endpoint to move cards to confirmed stage');
            }

            // Update positions in the old stage (if stage changed)
            if ($oldStage !== $newStage) {
                KanbanCard::where('stage', $oldStage)
                    ->where('position', '>', $kanbanCard->position)
                    ->decrement('position');
            }

            // Make room in the new stage
            KanbanCard::where('stage', $newStage)
                ->where('position', '>=', $newPosition)
                ->where('id', '!=', $kanbanCard->id)
                ->increment('position');

            // Update the card
            $kanbanCard->update([
                'stage' => $newStage,
                'position' => $newPosition,
            ]);
        });

        return back()->with('success', 'Card moved successfully');
    }

    /**
     * Confirm a card - move to confirmed stage and create schedule event.
     */
    public function confirm(Request $request, KanbanCard $kanbanCard)
    {
        $this->authorize('confirm', $kanbanCard);

        $validated = $request->validate([
            'start_datetime' => 'required|date',
            'end_datetime' => 'required|date|after:start_datetime',
            'venue' => 'nullable|string|max:255',
            'resource_ids' => 'nullable|array',
            'resource_ids.*' => 'exists:resources,id',
            'override_conflicts' => 'nullable|boolean',
        ]);

        // Load card relationships for conflict checking
        $kanbanCard->load(['members', 'staff']);

        // Create temporary event for conflict checking
        $tempEvent = new ScheduleEvent([
            'title' => $kanbanCard->title,
            'description' => $kanbanCard->description,
            'type' => $kanbanCard->type,
            'start_datetime' => $validated['start_datetime'],
            'end_datetime' => $validated['end_datetime'],
            'venue' => $validated['venue'],
            'status' => 'confirmed',
        ]);

        // Check for conflicts
        $conflicts = $this->conflictService->checkScheduleEventConflicts(
            $tempEvent,
            $kanbanCard->members->pluck('id')->toArray(),
            $kanbanCard->staff->pluck('id')->toArray(),
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

        // Create the schedule event and update the card
        DB::transaction(function () use ($validated, $request, $kanbanCard, $conflicts) {
            $event = ScheduleEvent::create([
                'title' => $kanbanCard->title,
                'description' => $kanbanCard->description,
                'type' => $kanbanCard->type,
                'start_datetime' => $validated['start_datetime'],
                'end_datetime' => $validated['end_datetime'],
                'venue' => $validated['venue'],
                'status' => 'confirmed',
                'kanban_card_id' => $kanbanCard->id,
                'created_by' => $request->user()->id,
                'conflict_notes' => $request->boolean('override_conflicts') && $conflicts->isNotEmpty()
                    ? 'Conflicts overridden by ' . $request->user()->name
                    : null,
            ]);

            // Copy relationships from card to event
            $event->members()->attach($kanbanCard->members->pluck('id'));
            $event->staff()->attach($kanbanCard->staff->pluck('id'));

            if (!empty($validated['resource_ids'])) {
                $resourceData = collect($validated['resource_ids'])->mapWithKeys(fn($id) => [$id => ['quantity' => 1]]);
                $event->resources()->attach($resourceData);
            }

            // Move card to confirmed stage
            $oldStage = $kanbanCard->stage;
            KanbanCard::where('stage', $oldStage)
                ->where('position', '>', $kanbanCard->position)
                ->decrement('position');

            $maxPosition = KanbanCard::where('stage', 'confirmed')->max('position') ?? 0;
            $kanbanCard->update([
                'stage' => 'confirmed',
                'position' => $maxPosition + 1,
            ]);

            // Log conflicts if overridden
            if ($request->boolean('override_conflicts') && $conflicts->isNotEmpty()) {
                $this->conflictService->logConflicts($event, $conflicts);

                foreach ($event->conflictLogs as $log) {
                    $this->conflictService->resolveConflict($log, $request->user()->id, 'overridden');
                }
            }
        });

        return redirect()->route('admin.kanban.index')
            ->with('success', 'Card confirmed and schedule event created');
    }

    /**
     * Delete a kanban card.
     */
    public function destroy(KanbanCard $kanbanCard)
    {
        $this->authorize('delete', $kanbanCard);

        DB::transaction(function () use ($kanbanCard) {
            $stage = $kanbanCard->stage;

            $kanbanCard->delete();

            // Reorder remaining cards in the stage
            KanbanCard::where('stage', $stage)
                ->where('position', '>', $kanbanCard->position)
                ->decrement('position');
        });

        return redirect()->route('admin.kanban.index')
            ->with('success', 'Kanban card deleted successfully');
    }
}
