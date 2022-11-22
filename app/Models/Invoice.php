<?php

namespace App\Models;

use App\Models\Enums\InvoiceStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $casts = [
        'status' => InvoiceStatus::class
    ];
}
