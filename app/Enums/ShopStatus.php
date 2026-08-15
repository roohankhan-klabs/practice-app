<?php

namespace App\Enums;

enum ShopStatus: string
{
    case Verified = 'verified';
    case Unverified = 'unverified';
    case Blocked = 'blocked';
}
