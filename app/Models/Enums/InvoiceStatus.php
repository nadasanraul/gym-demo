<?php
namespace App\Models\Enums;

enum InvoiceStatus: string
{
    case Outstanding = 'Outstanding';
    case Paid = 'Paid';
    case Void = 'Void';
}
