<?php

namespace App\Filament\Resources\VariantOptions\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class VariantOptionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('variant_type_id')
                    ->required()
                    ->numeric(),
                TextInput::make('name')
                    ->required(),
                TextInput::make('value')
                    ->required(),
                TextInput::make('hex_code'),
                Toggle::make('is_active')
                    ->required(),
            ]);
    }
}
