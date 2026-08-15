<?php

namespace App\Filament\Resources\Shops\Tables;

use App\Enums\ShopStatus;
use App\Filament\Resources\Shops\ShopResource;
use App\Filament\Resources\Users\UserResource;
use App\Models\Shop;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ShopsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                // ImageColumn::make('cover_image'),
                ImageColumn::make('logo'),
                TextColumn::make('user.name')
                    ->sortable()
                    ->url(fn(Shop $record) => UserResource::getUrl('view', ['record' => $record])),
                TextColumn::make('shop_name')
                    ->searchable()
                    ->url(fn(Shop $record) => ShopResource::getUrl('view', ['record' => $record])),
                TextColumn::make('whatsapp_number')
                    ->searchable(),
                TextColumn::make('contact_number')
                    ->searchable(),
                // TextColumn::make('address_id')
                //     ->searchable(),
                // TextColumn::make('shipping_policy')
                //     ->searchable(),
                // TextColumn::make('refund_policy')
                //     ->searchable(),
                // TextColumn::make('return_policy')
                //     ->searchable(),
                // TextColumn::make('privacy_policy')
                //     ->searchable(),
                // TextColumn::make('terms_of_service')
                //     ->searchable(),
                // TextColumn::make('google_maps_link')
                //     ->searchable(),
                TextColumn::make('avg_rating')
                    ->searchable(),
                TextColumn::make('total_reviews')
                    ->searchable(),
                TextColumn::make('status')
                    ->badge()
                    ->color(
                        fn(string $state): string => match ($state) {
                            ShopStatus::Verified->value => 'success',
                            ShopStatus::Unverified->value => 'warning',
                            ShopStatus::Blocked->value => 'danger',
                            default => 'secondary',
                        }
                    )
                    ->formatStateUsing(
                        fn(string $state): string => match ($state) {
                            ShopStatus::Verified->value => 'Verified',
                            ShopStatus::Unverified->value => 'Unverified',
                            ShopStatus::Blocked->value => 'Blocked',
                            default => $state,
                        }
                    )
                    ->searchable(),
                IconColumn::make('is_featured')
                    ->boolean(),
                TextColumn::make('shipping_fee_type')
                    ->searchable(),
                TextColumn::make('shipping_fee_amount')
                    ->searchable(),
                TextColumn::make('estimated_delivery_time')
                    ->searchable(),
                TextColumn::make('commission_percentage')
                    ->searchable(),
                // TextColumn::make('instagram')
                //     ->searchable(),
                // TextColumn::make('facebook')
                //     ->searchable(),
                // TextColumn::make('tiktok')
                //     ->searchable(),
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
