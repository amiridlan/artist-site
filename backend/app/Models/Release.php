<?php

namespace App\Models;

use App\Models\Concerns\HasTranslations;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Release extends Model
{
    use HasFactory, HasTranslations;

    public function getTranslatableFields(): array
    {
        return ['title', 'description'];
    }

    protected $fillable = [
        'slug',
        'title',
        'type',
        'release_date',
        'cover_image',
        'description',
        'tracks',
        'streaming_links',
    ];

    protected function casts(): array
    {
        return [
            'release_date' => 'date',
            'tracks' => 'array',
            'streaming_links' => 'array',
        ];
    }

    public function scopeType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function scopeLatest($query)
    {
        return $query->orderByDesc('release_date');
    }
}
