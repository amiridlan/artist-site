<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EventResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $locale = $request->query('lang');
        $translations = $locale && $locale !== 'en'
            ? $this->getTranslationsForLocale($locale)
            : [];

        $imageUrls = media_urls($this->image);

        return [
            'id' => 'event-' . str_pad((string) $this->id, 3, '0', STR_PAD_LEFT),
            'slug' => $this->slug,
            'title' => $translations['title'] ?? $this->title,
            'type' => $this->type,
            'status' => $this->status,
            'date' => $this->date->toDateString(),
            'endDate' => $this->end_date?->toDateString(),
            'venue' => $translations['venue'] ?? $this->venue,
            'location' => $translations['location'] ?? $this->location,
            'description' => $translations['description'] ?? $this->description,
            'ticketUrl' => $this->ticket_url,
            'image' => $imageUrls['webp']['medium'] ?? $imageUrls['webp']['original'],
            'imageSizes' => $imageUrls,
        ];
    }
}
