<?php

namespace App\Filament\Resources\Admins\Schemas;

use App\Enums\UserStatus;
use App\Models\Role;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class AdminForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Basic Information')
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('email')
                            ->label('Email address')
                            ->email()
                            ->required()
                            ->maxLength(255),
                        TextInput::make('phone')
                            ->tel()
                            ->placeholder('-'),
                        DateTimePicker::make('email_verified_at'),
                    ])
                    ->columns(2),

                Section::make('System & Role Settings')
                    ->schema([
                        Select::make('role_id')
                            ->label('Role')
                            ->relationship('role', 'name')
                            ->required()
                            ->default(Role::ADMIN),
                        Select::make('current_team_id')
                            ->label('Current Team')
                            ->relationship('currentTeam', 'name'),
                        Select::make('status')
                            ->options(UserStatus::class)
                            ->required()
                            ->default(UserStatus::ACTIVE->value),
                    ])
                    ->columns(3),

                Section::make('Associated Devices')
                    ->schema([
                        Select::make('devices')
                            ->relationship('devices', 'device_name')
                            ->multiple()
                            ->preload()
                            ->placeholder('Select devices associated with this admin'),
                    ]),

                Section::make('Security')
                    ->schema([
                        TextInput::make('password')
                            ->password()
                            ->dehydrated(fn ($state) => filled($state))
                            ->required(fn (string $operation): bool => $operation === 'create')
                            ->placeholder(fn (string $operation): string => $operation === 'create' ? 'Enter password' : 'Leave blank to keep current password'),
                    ]),
            ]);
    }
}
