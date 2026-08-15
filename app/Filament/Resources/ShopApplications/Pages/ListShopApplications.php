<?php

namespace App\Filament\Resources\ShopApplications\Pages;

use App\Filament\Resources\ShopApplications\ShopApplicationResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListShopApplications extends ListRecords
{
    protected static string $resource = ShopApplicationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
