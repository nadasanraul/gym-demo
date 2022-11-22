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
     * Defines the relation with User
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
