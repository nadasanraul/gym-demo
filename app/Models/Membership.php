<?php

namespace App\Models;

use App\Models\Enums\MembershipStatus;
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

    /**
     * Determine if the membership is not cancelled
     *
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->status === MembershipStatus::Active;
    }

    /**
     * Determine if the membership is not started yet
     *
     * @return bool
     */
    public function isNotStarted(): bool
    {
        return $this->start_date->isFuture();
    }

    /**
     * Determine if the membership is expired
     *
     * @return bool
     */
    public function isExpired(): bool
    {
        return $this->end_date->isPast();
    }

    /**
     * Determine if this membership has no credits left
     *
     * @return bool
     */
    public function isEmpty(): bool
    {
        return $this->credits <= 0;
    }
}
