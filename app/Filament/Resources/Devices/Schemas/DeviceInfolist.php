<?php

namespace App\Filament\Resources\Devices\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class DeviceInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Device Identifiers')
                    ->schema([
                        TextEntry::make('device_id')
                            ->placeholder('-'),
                        TextEntry::make('fingerprint')
                            ->placeholder('-'),
                    ])
                    ->columns(2),

                Section::make('Device Details')
                    ->schema([
                        TextEntry::make('device_name')
                            ->placeholder('-'),
                        TextEntry::make('device_manufacturer')
                            ->placeholder('-'),
                        TextEntry::make('device_type')
                            ->placeholder('-'),
                        TextEntry::make('device_os')
                            ->label('OS')
                            ->placeholder('-'),
                        TextEntry::make('device_os_version')
                            ->label('OS Version')
                            ->placeholder('-'),
                        IconEntry::make('is_mobile')
                            ->boolean(),
                    ])
                    ->columns(3),

                Section::make('Associated Users')
                    ->schema([
                        TextEntry::make('users.name')
                            ->label('Users')
                            ->badge()
                            ->placeholder('No users associated with this device'),
                    ]),

                Section::make('Activity Info')
                    ->schema([
                        TextEntry::make('last_ip_address')
                            ->label('Last IP Address')
                            ->placeholder('-'),
                        TextEntry::make('last_activity_at')
                            ->dateTime()
                            ->placeholder('-'),
                    ])
                    ->columns(2),

                Section::make('Metadata')
                    ->schema([
                        TextEntry::make('created_at')
                            ->dateTime()
                            ->placeholder('-'),
                        TextEntry::make('updated_at')
                            ->dateTime()
                            ->placeholder('-'),
                    ])
                    ->columns(2),
            ]);
    }
}
