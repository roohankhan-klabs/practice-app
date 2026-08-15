<?php

namespace App\Filament\Resources\VariantOptions\Pages;

use App\Filament\Resources\VariantOptions\VariantOptionResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewVariantOption extends ViewRecord
{
    protected static string $resource = VariantOptionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
