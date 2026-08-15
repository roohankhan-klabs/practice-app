<?php

namespace App\Filament\Resources\ShopApplications\Pages;

use App\Filament\Resources\ShopApplications\ShopApplicationResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewShopApplication extends ViewRecord
{
    protected static string $resource = ShopApplicationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
