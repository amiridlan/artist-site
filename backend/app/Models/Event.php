<?php

namespace App\Models;

use App\Models\Concerns\HasTranslations;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory, HasTranslations;

    public function getTranslatableFields(): array
    {
        return ['title', 'description', 'venue', 'location'];
    }

    protected $fillable = [
        'slug',
        'title',
        'type',
        'status',
        'date',
        'end_date',
        'venue',
        'location',
        'description',
        'ticket_url',
        'image',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'end_date' => 'date',
        ];
    }

    public function scopeUpcoming($query)
    {
        return $query->where('status', 'upcoming');
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
