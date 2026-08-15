<?php

namespace App\Filament\Resources\ShopStaff\Pages;

use App\Filament\Resources\ShopStaff\ShopStaffResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditShopStaff extends EditRecord
{
    protected static string $resource = ShopStaffResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
