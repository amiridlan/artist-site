<?php

namespace App\Services;

use App\Models\ConflictLog;
use App\Models\ScheduleEvent;
use Illuminate\Support\Collection;

class ConflictDetectionService
{
    public function checkScheduleEventConflicts(
        ScheduleEvent $event,
        array $memberIds = [],
        array $staffIds = [],
        array $resourceIds = []
    ): Collection {
        $conflicts = collect();

        foreach ($memberIds as $memberId) {
            $memberConflicts = $this->checkArtistDoubleBooking($event, $memberId);
            $conflicts = $conflicts->merge($memberConflicts);
        }

        foreach ($memberIds as $memberId) {
            $dayOffConflicts = $this->checkArtistDayOffConflict($event, $memberId);
            $conflicts = $conflicts->merge($dayOffConflicts);
        }

        foreach ($staffIds as $staffId) {
            $staffConflicts = $this->checkStaffAvailability($event, $staffId);
            $conflicts = $conflicts->merge($staffConflicts);
        }

        foreach ($resourceIds as $resourceId) {
            $resourceConflicts = $this->checkResourceAvailability($event, $resourceId);
            $conflicts = $conflicts->merge($resourceConflicts);
        }

        return $conflicts;
    }

    public function checkArtistDoubleBooking(ScheduleEvent $event, int $memberId): Collection
    {
        $conflicts = collect();

        $overlappingEvents = ScheduleEvent::confirmed()
            ->where('id', '!=', $event->id ?? 0)
            ->forMember($memberId)
            ->overlapping($event->start_datetime, $event->end_datetime)
            ->with('members')
            ->get();

        foreach ($overlappingEvents as $overlappingEvent) {
            $member = $overlappingEvent->members->find($memberId);
            $conflicts->push([
                'type' => 'artist_double_booking',
                'severity' => 'error',
                'message' => "Artist {$member->name_english} is already scheduled",
                'details' => [
                    'member_id' => $memberId,
                    'member_name' => $member->name_english,
                    'conflicting_event_id' => $overlappingEvent->id,
                ],
            ]);
        }

        return $conflicts;
    }

    public function checkArtistDayOffConflict(ScheduleEvent $event, int $memberId): Collection
    {
        $conflicts = collect();

        if ($event->type === 'day_off') {
            return $conflicts;
        }

        $dayOffEvents = ScheduleEvent::confirmed()
            ->where('id', '!=', $event->id ?? 0)
            ->where('type', 'day_off')
            ->forMember($memberId)
            ->overlapping($event->start_datetime, $event->end_datetime)
            ->with('members')
            ->get();

        foreach ($dayOffEvents as $dayOffEvent) {
            $member = $dayOffEvent->members->find($memberId);
            $conflicts->push([
                'type' => 'artist_day_off_conflict',
                'severity' => 'error',
                'message' => "Artist {$member->name_english} has a day-off scheduled",
                'details' => [
                    'member_id' => $memberId,
                    'member_name' => $member->name_english,
                    'day_off_event_id' => $dayOffEvent->id,
                ],
            ]);
        }

        return $conflicts;
    }

    public function checkStaffAvailability(ScheduleEvent $event, int $staffId): Collection
    {
        return collect();
    }

    public function checkResourceAvailability(ScheduleEvent $event, int $resourceId): Collection
    {
        return collect();
    }

    public function logConflicts($conflictable, Collection $conflicts): void
    {
        foreach ($conflicts as $conflict) {
            ConflictLog::create([
                'conflictable_type' => get_class($conflictable),
                'conflictable_id' => $conflictable->id,
                'conflict_type' => $conflict['type'],
                'details' => $conflict['details'],
                'resolution' => 'pending',
            ]);
        }
    }

    public function resolveConflict(ConflictLog $conflictLog, int $resolvedBy, string $resolution = 'resolved'): void
    {
        $conflictLog->update([
            'resolution' => $resolution,
            'resolved_by' => $resolvedBy,
            'resolved_at' => now(),
        ]);
    }
}
