<?php

namespace App\Filament\Resources\VariantTypes\Pages;

use App\Filament\Resources\VariantTypes\VariantTypeResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewVariantType extends ViewRecord
{
    protected static string $resource = VariantTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
