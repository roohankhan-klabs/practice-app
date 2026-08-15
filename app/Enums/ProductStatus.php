<?php

namespace App\Enums;

enum ProductStatus: string
{
    case Pending = 'pending';
    case Rejected = 'rejected';
    case Approved = 'approved';
}
