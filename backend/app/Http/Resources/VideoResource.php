<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VideoResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $locale = $request->query('lang');
        $translations = $locale && $locale !== 'en'
            ? $this->getTranslationsForLocale($locale)
            : [];

        $thumbnailUrls = media_urls($this->thumbnail);

        return [
            'id' => 'video-' . str_pad((string) $this->id, 3, '0', STR_PAD_LEFT),
            'slug' => $this->slug,
            'title' => $translations['title'] ?? $this->title,
            'type' => $this->type,
            'youtubeId' => $this->youtube_id,
            'thumbnail' => $thumbnailUrls['webp']['medium'] ?? $thumbnailUrls['webp']['original'],
            'thumbnailSizes' => $thumbnailUrls,
            'date' => $this->date->toDateString(),
            'duration' => $this->duration,
            'description' => $translations['description'] ?? $this->description,
            'venue' => $this->venue,
        ];
    }
}
