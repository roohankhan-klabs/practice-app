<?php

namespace App\Enums;

enum Status: string
{
    case Active = 'active';
    case Inactive = 'inactive';

    case Pending = 'pending';
    case Rejected = 'rejected';
    case Approved = 'approved';
    case OnHold = 'on_hold';
    case Cancelled = 'cancelled';

    case Draft = 'draft';

    case Verified = 'verified';
    case Expired = 'expired';
}
