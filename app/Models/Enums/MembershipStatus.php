<?php

namespace App\Models\Enums;

enum MembershipStatus: string
{
    case Active = 'Active';
    case Cancelled = 'Cancelled';
}
