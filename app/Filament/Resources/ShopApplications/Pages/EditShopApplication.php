<?php

namespace App\Filament\Resources\ShopApplications\Pages;

use App\Filament\Resources\ShopApplications\ShopApplicationResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditShopApplication extends EditRecord
{
    protected static string $resource = ShopApplicationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
