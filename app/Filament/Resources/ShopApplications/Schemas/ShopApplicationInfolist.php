<?php

namespace App\Filament\Resources\ShopApplications\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class ShopApplicationInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('user_id')
                    ->numeric(),
                TextEntry::make('shop_name'),
                TextEntry::make('email')
                    ->label('Email address'),
                TextEntry::make('country_code'),
                TextEntry::make('phone'),
                TextEntry::make('vat_registration_number')
                    ->placeholder('-'),
                TextEntry::make('commercial_registration_number')
                    ->placeholder('-'),
                TextEntry::make('status'),
                TextEntry::make('rejection_reason')
                    ->placeholder('-'),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
