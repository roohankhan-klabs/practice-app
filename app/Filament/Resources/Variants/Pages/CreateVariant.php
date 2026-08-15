<?php

namespace App\Filament\Resources\Variants\Pages;

use App\Filament\Resources\Variants\VariantResource;
use Filament\Resources\Pages\CreateRecord;

class CreateVariant extends CreateRecord
{
    protected static string $resource = VariantResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['variant_option_ids'] = VariantResource::getVariantOptionIdsFromFormData($data);

        unset($data['selected_options']);

        return $data;
    }
}
