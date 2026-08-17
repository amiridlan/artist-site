<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Contract;
use App\Models\ScheduleEvent;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CalendarController extends Controller
{
    /**
     * Display the calendar view.
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', ScheduleEvent::class);

        return Inertia::render('Admin/Calendar/Index', [
            'eventTypes' => [
                'artist_performance' => 'Artist Performance',
                'artist_appearance' => 'Artist Appearance',
                'content_filming' => 'Content Filming',
                'practice_day' => 'Practice Day',
                'day_off' => 'Day Off',
                'staff_event' => 'Staff Event',
                'social_media_post' => 'Social Media Post',
            ],
            'members' => \App\Models\Member::where('status', 'active')
                ->orderBy('name_english')
                ->get(['id', 'name_english as name'])
                ->toArray(),
            'showContractRenewals' => $request->user()->can('view-contracts'),
        ]);
    }

    /**
     * Return events in FullCalendar format.
     */
    public function events(Request $request)
    {
        $this->authorize('viewAny', ScheduleEvent::class);

        $query = ScheduleEvent::with(['members', 'createdBy']);

        // Filter by date range (FullCalendar sends start and end)
        if ($request->filled('start')) {
            $query->where('end_datetime', '>=', $request->start);
        }
        if ($request->filled('end')) {
            $query->where('start_datetime', '<=', $request->end);
        }

        // Filter by event types (comma-separated)
        if ($request->filled('types')) {
            $types = explode(',', $request->types);
            $query->whereIn('type', $types);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by member
        if ($request->filled('member')) {
            $query->whereHas('members', function ($q) use ($request) {
                $q->where('members.id', $request->member);
            });
        }

        // Filter by "My Events Only"
        if ($request->boolean('my_events')) {
            $query->where('created_by', $request->user()->id);
        }

        // Artists only see their own events (unless using filters)
        if ($request->user()->isArtist() && $request->user()->member_id && !$request->boolean('my_events')) {
            $query->forMember($request->user()->member_id);
        }

        $events = $query->get();

        // Transform to FullCalendar format
        $calendarEvents = $events->map(function ($event) use ($request) {
            $canEdit = $request->user()->can('update', $event);

            return [
                'id' => $event->id,
                'title' => $event->title,
                'start' => $event->start_datetime->toISOString(),
                'end' => $event->end_datetime->toISOString(),
                'backgroundColor' => $this->getEventColor($event->type),
                'borderColor' => $this->getEventColor($event->type),
                'extendedProps' => [
                    'type' => $event->type,
                    'status' => $event->status,
                    'venue' => $event->venue,
                    'description' => $event->description,
                    'members' => $event->members->pluck('name_english')->toArray(),
                    'createdBy' => $event->createdBy?->name,
                    'canEdit' => $canEdit,
                ],
            ];
        });

        if ($request->boolean('show_contract_renewals') && $request->user()->can('view-contracts')) {
            $contractsQuery = Contract::with('member')->where('status', 'active');

            if ($request->filled('start')) {
                $contractsQuery->where('end_date', '>=', $request->start);
            }
            if ($request->filled('end')) {
                $contractsQuery->where('end_date', '<=', $request->end);
            }

            $contractEvents = $contractsQuery->get()->map(function (Contract $contract) use ($request) {
                return [
                    'id' => 'contract-' . $contract->id,
                    'title' => "Contract Renewal — {$contract->member->name_english}",
                    'start' => $contract->end_date->toDateString(),
                    'end' => $contract->end_date->toDateString(),
                    'allDay' => true,
                    'backgroundColor' => '#f59e0b',
                    'borderColor' => '#f59e0b',
                    'extendedProps' => [
                        'type' => 'contract_renewal',
                        'status' => $contract->status,
                        'members' => [$contract->member->name_english],
                        'canEdit' => $request->user()->can('manage-contracts'),
                        'contractId' => $contract->id,
                    ],
                ];
            });

            $calendarEvents = $calendarEvents->merge($contractEvents);
        }

        return response()->json($calendarEvents);
    }

    /**
     * Get color code for event type.
     */
    protected function getEventColor(string $type): string
    {
        return match ($type) {
            'artist_performance' => '#ef4444', // Red
            'artist_appearance' => '#f97316', // Orange
            'content_filming' => '#8b5cf6', // Purple
            'practice_day' => '#3b82f6', // Blue
            'day_off' => '#10b981', // Green
            'staff_event' => '#6366f1', // Indigo
            'social_media_post' => '#ec4899', // Pink
            default => '#6b7280', // Gray
        };
    }
}
