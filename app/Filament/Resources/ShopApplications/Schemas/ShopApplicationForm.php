<?php

namespace App\Filament\Resources\ShopApplications\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ShopApplicationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('user_id')
                    ->required()
                    ->numeric(),
                TextInput::make('shop_name')
                    ->required(),
                TextInput::make('email')
                    ->label('Email address')
                    ->email()
                    ->required(),
                TextInput::make('country_code')
                    ->required(),
                TextInput::make('phone')
                    ->tel()
                    ->required(),
                TextInput::make('vat_registration_number'),
                TextInput::make('commercial_registration_number'),
                TextInput::make('status')
                    ->required()
                    ->default('pending'),
                TextInput::make('rejection_reason'),
            ]);
    }
}
