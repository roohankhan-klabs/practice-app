<?php

namespace App\Filament\Resources\VariantTypes\Pages;

use App\Filament\Resources\VariantTypes\VariantTypeResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditVariantType extends EditRecord
{
    protected static string $resource = VariantTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
