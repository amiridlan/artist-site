<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MemberResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $locale = $request->query('lang');
        $translations = $locale && $locale !== 'en'
            ? $this->getTranslationsForLocale($locale)
            : [];

        $photoUrls = media_urls($this->photo);
        $coverUrls = media_urls($this->cover_image);

        return [
            'id' => $this->slug,
            'name' => [
                'english' => $this->name_english,
                'native' => $this->name_native,
            ],
            'nickname' => $this->nickname,
            // Default URLs (WebP medium for best compatibility/quality balance)
            'photo' => $photoUrls['webp']['medium'] ?? $photoUrls['webp']['original'],
            'coverImage' => $coverUrls['webp']['large'] ?? $coverUrls['webp']['original'],
            // All sizes and formats for responsive images
            'photoSizes' => $photoUrls,
            'coverImageSizes' => $coverUrls,
            'generation' => $this->generation,
            'birthdate' => $this->birthdate,
            'age' => $this->age,
            'height' => $this->height,
            'bloodType' => $this->blood_type,
            'hometown' => $translations['hometown'] ?? $this->hometown,
            'hobbies' => $this->hobbies,
            'bio' => $translations['bio'] ?? $this->bio,
            'joinDate' => $this->join_date?->toDateString(),
            'status' => $this->status,
            'color' => $this->color,
            'social' => $this->social,
        ];
    }
}
