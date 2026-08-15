<?php

namespace App\Filament\Resources\ShopStaff\Pages;

use App\Filament\Resources\ShopStaff\ShopStaffResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListShopStaff extends ListRecords
{
    protected static string $resource = ShopStaffResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
