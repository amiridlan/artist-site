<?php

namespace App\Models;

use App\Models\Concerns\HasTranslations;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class News extends Model
{
    use HasFactory, HasTranslations;

    public function getTranslatableFields(): array
    {
        return ['title', 'excerpt', 'content'];
    }

    protected $fillable = [
        'slug',
        'title',
        'excerpt',
        'content',
        'category',
        'date',
        'image',
        'featured',
        'published',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'featured' => 'boolean',
            'published' => 'boolean',
        ];
    }

    public function scopePublished($query)
    {
        return $query->where('published', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('featured', true);
    }

    public function scopeCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    public function scopeLatest($query)
    {
        return $query->orderByDesc('date');
    }
}
