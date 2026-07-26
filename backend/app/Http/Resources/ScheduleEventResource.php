<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ScheduleEventResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'type' => $this->type,
            'startDatetime' => $this->start_datetime?->toISOString(),
            'endDatetime' => $this->end_datetime?->toISOString(),
            'venue' => $this->venue,
            'status' => $this->status,
            'conflictNotes' => $this->conflict_notes,
            'kanbanCardId' => $this->kanban_card_id,
            'createdBy' => $this->created_by,
            'createdAt' => $this->created_at?->toISOString(),
            'updatedAt' => $this->updated_at?->toISOString(),

            // Relationships (loaded conditionally)
            'members' => $this->whenLoaded('members', fn() => $this->members->map(fn($m) => [
                'id' => $m->id,
                'nameEnglish' => $m->name_english,
                'slug' => $m->slug,
            ])),
            'staff' => $this->whenLoaded('staff', fn() => $this->staff->map(fn($s) => [
                'id' => $s->id,
                'name' => $s->name,
                'email' => $s->email,
            ])),
            'resources' => $this->whenLoaded('resources', fn() => $this->resources->map(fn($r) => [
                'id' => $r->id,
                'name' => $r->name,
                'type' => $r->type,
                'quantity' => $r->pivot->quantity,
            ])),
            'createdByUser' => $this->whenLoaded('createdBy', fn() => [
                'id' => $this->createdBy->id,
                'name' => $this->createdBy->name,
            ]),
            'conflictLogs' => $this->whenLoaded('conflictLogs', fn() =>
                ConflictLogResource::collection($this->conflictLogs)
            ),
        ];
    }
}
