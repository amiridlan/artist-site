<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ConflictLogResource extends JsonResource
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
            'conflictableType' => $this->conflictable_type,
            'conflictableId' => $this->conflictable_id,
            'conflictType' => $this->conflict_type,
            'details' => $this->details,
            'resolution' => $this->resolution,
            'resolvedBy' => $this->resolved_by,
            'resolvedAt' => $this->resolved_at?->toISOString(),
            'createdAt' => $this->created_at?->toISOString(),
            'updatedAt' => $this->updated_at?->toISOString(),

            // Relationships
            'resolvedByUser' => $this->whenLoaded('resolvedBy', fn() => [
                'id' => $this->resolvedBy->id,
                'name' => $this->resolvedBy->name,
            ]),
        ];
    }
}
