<?php

namespace App\Filament\Resources\Devices\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class DevicesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('device_id')
                    ->searchable(),
                TextColumn::make('fingerprint')
                    ->searchable(),
                TextColumn::make('app_version')
                    ->searchable(),
                TextColumn::make('device_os')
                    ->searchable(),
                TextColumn::make('device_os_version')
                    ->searchable(),
                TextColumn::make('device_type')
                    ->searchable(),
                TextColumn::make('device_name')
                    ->searchable(),
                TextColumn::make('device_manufacturer')
                    ->searchable(),
                IconColumn::make('is_mobile')
                    ->boolean(),
                TextColumn::make('last_ip_address')
                    ->searchable(),
                TextColumn::make('last_activity_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('users.name')
                    ->label('Associated Users')
                    ->badge()
                    ->searchable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
