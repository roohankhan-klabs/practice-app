<?php

namespace App\Filament\Resources\PaymentLogs\Pages;

use App\Filament\Resources\PaymentLogs\PaymentLogResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePaymentLog extends CreateRecord
{
    protected static string $resource = PaymentLogResource::class;
}
