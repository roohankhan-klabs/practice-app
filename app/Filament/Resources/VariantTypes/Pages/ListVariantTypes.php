<?php

namespace App\Filament\Resources\VariantTypes\Pages;

use App\Filament\Resources\VariantTypes\VariantTypeResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListVariantTypes extends ListRecords
{
    protected static string $resource = VariantTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
