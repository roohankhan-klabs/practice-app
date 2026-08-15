<?php

namespace App\Filament\Resources\Addresses\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class AddressForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('user_id')
                    ->numeric(),
                TextInput::make('shop_id')
                    ->numeric(),
                TextInput::make('address_line_1')
                    ->required(),
                TextInput::make('address_line_2'),
                TextInput::make('preffered_contact_number'),
                TextInput::make('postal_code'),
                TextInput::make('city_id')
                    ->required(),
                TextInput::make('state_id')
                    ->required(),
                TextInput::make('country_id')
                    ->required(),
                Toggle::make('is_default')
                    ->required(),
            ]);
    }
}
