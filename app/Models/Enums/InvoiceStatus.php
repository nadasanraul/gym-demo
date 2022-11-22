<?php
namespace App\Models\Enums;

enum InvoiceStatus
{
    case Outstanding;
    case Paid;
    case Void;
}
