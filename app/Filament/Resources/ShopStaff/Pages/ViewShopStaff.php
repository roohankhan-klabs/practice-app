<?php

namespace App\Filament\Resources\ShopStaff\Pages;

use App\Filament\Resources\ShopStaff\ShopStaffResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewShopStaff extends ViewRecord
{
    protected static string $resource = ShopStaffResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
