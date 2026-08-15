<?php

namespace App\Filament\Resources\PaymentLogs\Pages;

use App\Filament\Resources\PaymentLogs\PaymentLogResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPaymentLogs extends ListRecords
{
    protected static string $resource = PaymentLogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
