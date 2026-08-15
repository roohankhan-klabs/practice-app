<?php

namespace App\Filament\Resources\Devices\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class DeviceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('device_id'),
                TextInput::make('fingerprint'),
                TextInput::make('app_version'),
                TextInput::make('device_os'),
                TextInput::make('device_os_version'),
                TextInput::make('device_type'),
                TextInput::make('device_name'),
                TextInput::make('device_manufacturer'),
                Toggle::make('is_mobile')
                    ->required(),
                TextInput::make('last_ip_address'),
                DateTimePicker::make('last_activity_at'),
            ]);
    }
}
