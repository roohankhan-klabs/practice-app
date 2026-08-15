<?php

namespace App\Filament\Resources\Devices\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class DeviceInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('device_id')
                    ->placeholder('-'),
                TextEntry::make('fingerprint')
                    ->placeholder('-'),
                TextEntry::make('app_version')
                    ->placeholder('-'),
                TextEntry::make('device_os')
                    ->placeholder('-'),
                TextEntry::make('device_os_version')
                    ->placeholder('-'),
                TextEntry::make('device_type')
                    ->placeholder('-'),
                TextEntry::make('device_name')
                    ->placeholder('-'),
                TextEntry::make('device_manufacturer')
                    ->placeholder('-'),
                IconEntry::make('is_mobile')
                    ->boolean(),
                TextEntry::make('last_ip_address')
                    ->placeholder('-'),
                TextEntry::make('last_activity_at')
                    ->dateTime()
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
