<?php

namespace App\Filament\Resources\Addresses\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class AddressInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('user_id')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('shop_id')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('address_line_1'),
                TextEntry::make('address_line_2')
                    ->placeholder('-'),
                TextEntry::make('preffered_contact_number')
                    ->placeholder('-'),
                TextEntry::make('postal_code')
                    ->placeholder('-'),
                TextEntry::make('city_id'),
                TextEntry::make('state_id'),
                TextEntry::make('country_id'),
                IconEntry::make('is_default')
                    ->boolean(),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
