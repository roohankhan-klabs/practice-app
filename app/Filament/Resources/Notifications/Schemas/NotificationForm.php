<?php

namespace App\Filament\Resources\Notifications\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class NotificationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('user_id')
                    ->required()
                    ->numeric(),
                TextInput::make('device_id')
                    ->required()
                    ->numeric(),
                TextInput::make('type')
                    ->required(),
                TextInput::make('title')
                    ->required(),
                TextInput::make('data')
                    ->required(),
                TextInput::make('reference_type'),
                TextInput::make('reference_id'),
                Toggle::make('is_sent')
                    ->required(),
                Toggle::make('is_read')
                    ->required(),
                DateTimePicker::make('sent_at'),
                DateTimePicker::make('read_at'),
            ]);
    }
}
