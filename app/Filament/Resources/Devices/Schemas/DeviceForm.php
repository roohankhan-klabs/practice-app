<?php

namespace App\Filament\Resources\Devices\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class DeviceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Device Identifiers')
                    ->schema([
                        TextInput::make('device_id')
                            ->maxLength(255),
                        TextInput::make('fingerprint')
                            ->maxLength(255),
                    ])
                    ->columns(2),

                Section::make('Device Details')
                    ->schema([
                        TextInput::make('device_name')
                            ->maxLength(255),
                        TextInput::make('device_manufacturer')
                            ->maxLength(255),
                        TextInput::make('device_type')
                            ->maxLength(255),
                        TextInput::make('device_os')
                            ->label('OS')
                            ->maxLength(255),
                        TextInput::make('device_os_version')
                            ->label('OS Version')
                            ->maxLength(255),
                        Toggle::make('is_mobile')
                            ->required(),
                    ])
                    ->columns(3),

                Section::make('Associated Users')
                    ->schema([
                        Select::make('users')
                            ->relationship('users', 'name')
                            ->multiple()
                            ->preload()
                            ->placeholder('Select users associated with this device'),
                    ]),

                Section::make('Activity Info')
                    ->schema([
                        TextInput::make('last_ip_address')
                            ->label('Last IP Address')
                            ->ip(),
                        DateTimePicker::make('last_activity_at'),
                    ])
                    ->columns(2),
            ]);
    }
}
