<?php

namespace App\Enums;

enum PaymentStatus: string
{
    case COD_PENDING = 'cod_pending';
    case PENDING = 'pending';
    case PROCESSING = 'processing';
    case SUCCESS = 'success';
    case FAILED = 'failed';
    case CANCELLED = 'cancelled';
    case REFUNDED = 'refunded';
}
