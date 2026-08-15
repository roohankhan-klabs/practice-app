<?php

namespace App\Filament\Resources\Countries\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class CountryInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('name'),
                TextEntry::make('code'),
                TextEntry::make('iso_code'),
                TextEntry::make('phone_code'),
                TextEntry::make('phone_number_digits'),
                TextEntry::make('country_code'),
                TextEntry::make('currency'),
                TextEntry::make('currency_code'),
                TextEntry::make('currency_symbol'),
                TextEntry::make('currency_exchange_rate'),
                TextEntry::make('currency_exchange_rate_date'),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
