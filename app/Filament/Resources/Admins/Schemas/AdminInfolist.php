<?php

namespace App\Filament\Resources\Admins\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class AdminInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Basic Information')
                    ->schema([
                        TextEntry::make('name'),
                        TextEntry::make('email')
                            ->label('Email address'),
                        TextEntry::make('phone')
                            ->placeholder('-'),
                        TextEntry::make('email_verified_at')
                            ->dateTime()
                            ->placeholder('-'),
                    ])
                    ->columns(2),

                Section::make('System & Associations')
                    ->schema([
                        TextEntry::make('role.name')
                            ->label('Role')
                            ->placeholder('-'),
                        TextEntry::make('currentTeam.name')
                            ->label('Current team')
                            ->placeholder('-'),
                        TextEntry::make('status')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'active' => 'success',
                                'inactive' => 'warning',
                                'suspended', 'blocked' => 'danger',
                                default => 'secondary',
                            })
                            ->placeholder('-'),
                    ])
                    ->columns(3),

                Section::make('Associated Devices')
                    ->schema([
                        TextEntry::make('devices.device_name')
                            ->label('Devices')
                            ->badge()
                            ->placeholder('No devices associated with this admin'),
                    ]),

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
