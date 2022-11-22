<?php

namespace App\Models;

use App\Models\Enums\InvoiceStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Invoice extends Model
{
    use HasFactory;

    /**
     * Attributes that should be cast to different types
     *
     * @var array<string, mixed>
     */
    protected $casts = [
        'status' => InvoiceStatus::class,
    ];

    /**
     * Attributes that are mass assignable
     *
     * @var string[]
     */
    protected $fillable = [
        'status',
        'description',
        'amount',
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
     * Defines the relationship with InvoiceLine
     *
     * @return HasMany
     */
    public function lines(): HasMany
    {
        return $this->hasMany(InvoiceLine::class);
    }

    /**
     * Defines the relationship with User
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
