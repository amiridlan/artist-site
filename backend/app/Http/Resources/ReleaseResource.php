<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReleaseResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $locale = $request->query('lang');
        $translations = $locale && $locale !== 'en'
            ? $this->getTranslationsForLocale($locale)
            : [];

        return [
            'id' => 'release-' . str_pad((string) $this->id, 3, '0', STR_PAD_LEFT),
            'slug' => $this->slug,
            'title' => $translations['title'] ?? $this->title,
            'type' => $this->type,
            'releaseDate' => $this->release_date->toDateString(),
            'coverImage' => $this->cover_image,
            'description' => $translations['description'] ?? $this->description,
            'tracks' => $this->tracks,
            'streamingLinks' => $this->streaming_links,
        ];
    }
}
