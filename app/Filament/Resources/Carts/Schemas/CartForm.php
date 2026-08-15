<?php

namespace App\Filament\Resources\Carts\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class CartForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('user_id')
                    ->numeric(),
                TextInput::make('device_id')
                    ->numeric(),
                TextInput::make('status'),
            ]);
    }
}
