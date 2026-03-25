<?php

namespace App\Models;

use App\Models\Concerns\HasTranslations;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Member extends Model
{
    use HasFactory, HasTranslations;

    public function getTranslatableFields(): array
    {
        return ['bio', 'hometown'];
    }

    protected $fillable = [
        'slug',
        'name_english',
        'name_native',
        'nickname',
        'photo',
        'cover_image',
        'generation',
        'birthdate',
        'age',
        'height',
        'blood_type',
        'hometown',
        'hobbies',
        'bio',
        'join_date',
        'status',
        'color',
        'social',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'hobbies' => 'array',
            'social' => 'array',
            'join_date' => 'date',
        ];
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeGeneration($query, string $generation)
    {
        return $query->where('generation', $generation);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name_english');
    }
}
