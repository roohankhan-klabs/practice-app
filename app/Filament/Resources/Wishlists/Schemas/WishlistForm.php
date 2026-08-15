<?php

namespace App\Filament\Resources\Wishlists\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class WishlistForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('user_id')
                    ->numeric(),
                TextInput::make('device_id')
                    ->numeric(),
                TextInput::make('product_id')
                    ->required()
                    ->numeric(),
            ]);
    }
}
