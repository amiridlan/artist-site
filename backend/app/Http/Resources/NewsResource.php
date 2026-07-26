<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NewsResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $locale = $request->query('lang');
        $translations = $locale && $locale !== 'en'
            ? $this->getTranslationsForLocale($locale)
            : [];

        // Safely get image URLs with error handling
        try {
            $imageUrls = media_urls($this->image);
        } catch (\Exception $e) {
            \Log::error('Failed to get media URLs for news article', [
                'article_id' => $this->id,
                'image_path' => $this->image,
                'error' => $e->getMessage()
            ]);
            $imageUrls = [
                'webp' => ['original' => null, 'medium' => null],
                'avif' => ['original' => null, 'medium' => null]
            ];
        }

        return [
            'id' => 'news-' . str_pad((string) $this->id, 3, '0', STR_PAD_LEFT),
            'slug' => $this->slug,
            'title' => $translations['title'] ?? $this->title,
            'excerpt' => $translations['excerpt'] ?? $this->excerpt,
            'content' => $this->when($request->routeIs('api.news.show'), $translations['content'] ?? $this->content),
            'category' => $this->category,
            'date' => $this->date->toDateString(),
            'image' => $imageUrls['webp']['medium'] ?? $imageUrls['webp']['original'],
            'imageSizes' => $imageUrls,
            'featured' => $this->featured,
        ];
    }
}
