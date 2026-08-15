<?php

namespace App\Enums;

enum Status: string
{
    case Active = 'active';
    case Inactive = 'inactive';

    case Draft = 'draft';

    case Verified = 'verified';
    case Expired = 'expired';
}
