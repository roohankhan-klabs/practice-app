<?php

namespace App\Filament\Resources\PaymentLogs\Pages;

use App\Filament\Resources\PaymentLogs\PaymentLogResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditPaymentLog extends EditRecord
{
    protected static string $resource = PaymentLogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
