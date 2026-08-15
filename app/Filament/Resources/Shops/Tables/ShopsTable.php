<?php

namespace App\Filament\Resources\Shops\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ShopsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user_id')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('shop_name')
                    ->searchable(),
                ImageColumn::make('cover_image'),
                TextColumn::make('logo')
                    ->searchable(),
                TextColumn::make('whatsapp_number')
                    ->searchable(),
                TextColumn::make('contact_number')
                    ->searchable(),
                TextColumn::make('address_id')
                    ->searchable(),
                TextColumn::make('shipping_policy')
                    ->searchable(),
                TextColumn::make('refund_policy')
                    ->searchable(),
                TextColumn::make('return_policy')
                    ->searchable(),
                TextColumn::make('privacy_policy')
                    ->searchable(),
                TextColumn::make('terms_of_service')
                    ->searchable(),
                TextColumn::make('google_maps_link')
                    ->searchable(),
                TextColumn::make('avg_rating')
                    ->searchable(),
                TextColumn::make('total_reviews')
                    ->searchable(),
                TextColumn::make('status')
                    ->searchable(),
                TextColumn::make('is_featured')
                    ->searchable(),
                TextColumn::make('shipping_fee_type')
                    ->searchable(),
                TextColumn::make('shipping_fee_amount')
                    ->searchable(),
                TextColumn::make('estimated_delivery_time')
                    ->searchable(),
                TextColumn::make('commission_percentage')
                    ->searchable(),
                TextColumn::make('instagram')
                    ->searchable(),
                TextColumn::make('facebook')
                    ->searchable(),
                TextColumn::make('tiktok')
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
