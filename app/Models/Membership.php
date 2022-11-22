<?php

namespace App\Models;

use App\Models\Enums\MembershipStatus;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Membership extends Model
{
    use HasFactory;

    /**
     * Attributes that should be cast to different types
     *
     * @var array<string, mixed>
     */
    protected $casts = [
        'status' => MembershipStatus::class,
        'start_date' => 'datetime',
        'end_date' => 'datetime',
    ];

    /**
     * Attributes that should be hidden for serialization
     *
     * @var string[]
     */
    protected $hidden = [
        'user_id',
    ];

    /**
     * Attributes that are mass assignable
     *
     * @var string[]
     */
    protected $fillable = [
        'credits',
    ];

    /**
     * Defines the relation with User
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isActive(): bool
    {
        return $this->status === MembershipStatus::Active;
    }

    public function isNotStarted(): bool
    {
        return $this->start_date->isFuture();
    }

    public function isExpired(): bool
    {
        return $this->end_date->isPast();
    }

    public function isEmpty(): bool
    {
        return $this->credits <= 0;
    }
}
