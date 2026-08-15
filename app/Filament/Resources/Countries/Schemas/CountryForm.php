<?php

namespace App\Filament\Resources\Countries\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class CountryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('code')
                    ->required(),
                TextInput::make('iso_code')
                    ->required(),
                TextInput::make('phone_code')
                    ->tel()
                    ->required(),
                TextInput::make('phone_number_digits')
                    ->tel()
                    ->required()
                    ->default('10'),
                TextInput::make('country_code')
                    ->required(),
                TextInput::make('currency')
                    ->required(),
                TextInput::make('currency_code')
                    ->required(),
                TextInput::make('currency_symbol')
                    ->required(),
                TextInput::make('currency_exchange_rate')
                    ->required(),
                TextInput::make('currency_exchange_rate_date')
                    ->required(),
            ]);
    }
}
