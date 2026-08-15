<?php

namespace App\Filament\Resources\Products\Tables;

use App\Filament\Resources\Shops\ShopResource;
use App\Filament\Resources\SubCategories\SubCategoryResource;
use App\Models\Product;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Support\View\Components\BadgeComponent;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ProductsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('shop.shop_name')
                    ->searchable()
                    ->sortable()
                    ->url(
                        fn(Product $record): ?string =>
                        $record->shop
                            ? ShopResource::getUrl('view', [
                                'record' => $record->shop,
                            ])
                            : null
                    ),
                TextColumn::make('subcategory.name')
                    ->searchable()
                    ->url(
                        fn(Product $record): ?string =>
                        $record->subcategory
                            ? SubCategoryResource::getUrl('view', [
                                'record' => $record->subcategory,
                            ])
                            : null
                    )
                    ->sortable(),
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('price')
                    ->money()
                    ->sortable(),
                TextColumn::make('shipping_price')
                    ->money()
                    ->sortable(),
                TextColumn::make('stock')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('low_stock_threshold')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('discount_type')
                    ->searchable(),
                TextColumn::make('discount_value')
                    ->numeric()
                    ->sortable(),
                IconColumn::make('show_in_app')
                    ->boolean(),
                IconColumn::make('is_featured')
                    ->boolean(),
                TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn(string $state) => strtoupper($state))
                    ->color(
                        fn(string $state): string => match ($state) {
                            'active' => 'success',
                            'inactive' => 'danger',
                            default => 'secondary',
                        }
                    )
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
