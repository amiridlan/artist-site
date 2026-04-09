<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Laravel\Sanctum\HasApiTokens;

class FanclubMember extends Model implements AuthenticatableContract
{
    use HasApiTokens, Authenticatable, HasFactory;

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'tier',
        'status',
        'benefits',
        'joined_at',
        'expires_at',
        'notes',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'benefits'          => 'array',
            'joined_at'         => 'date',
            'expires_at'        => 'date',
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
        ];
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeTier($query, string $tier)
    {
        return $query->where('tier', $tier);
    }

    public function isActive(): bool
    {
        // Cancelled members retain access through the end of their expires_at date.
        // "End of day" means 23:59:59 on the expires_at date.
        $hasValidPeriod = $this->expires_at === null
            || $this->expires_at->copy()->endOfDay()->isFuture();

        return in_array($this->status, ['active', 'cancelled']) && $hasValidPeriod;
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(FanclubSubscription::class);
    }
}
