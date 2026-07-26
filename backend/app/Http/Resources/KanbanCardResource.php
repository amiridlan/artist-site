<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class KanbanCardResource extends JsonResource
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
            'stage' => $this->stage,
            'dueDate' => $this->due_date?->toDateString(),
            'position' => $this->position,
            'createdBy' => $this->created_by,
            'createdAt' => $this->created_at?->toISOString(),
            'updatedAt' => $this->updated_at?->toISOString(),

            // Relationships
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
            'scheduleEvent' => $this->whenLoaded('scheduleEvent', fn() =>
                $this->scheduleEvent ? new ScheduleEventResource($this->scheduleEvent) : null
            ),
            'createdByUser' => $this->whenLoaded('createdBy', fn() => [
                'id' => $this->createdBy->id,
                'name' => $this->createdBy->name,
            ]),
        ];
    }
}
