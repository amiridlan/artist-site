<?php

namespace App\Models;

use App\Models\Concerns\HasTranslations;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Video extends Model
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
        'youtube_id',
        'thumbnail',
        'date',
        'duration',
        'description',
        'venue',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
        ];
    }

    public function scopeType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function scopeLatest($query)
    {
        return $query->orderByDesc('date');
    }
}
