<?php

namespace App\Filament\Resources\VariantOptions\Pages;

use App\Filament\Resources\VariantOptions\VariantOptionResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditVariantOption extends EditRecord
{
    protected static string $resource = VariantOptionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
