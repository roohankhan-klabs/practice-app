<?php

namespace App\Filament\Resources\Products\Schemas;

use App\Filament\Resources\Categories\CategoryResource;
use App\Filament\Resources\Shops\ShopResource;
use App\Filament\Resources\SubCategories\SubCategoryResource;
use App\Models\Product;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ProductInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Shop')->schema([
                    TextEntry::make('shop.shop_name')->url(fn (Product $record) => $record->shop ?
                        ShopResource::getUrl('view', ['record' => $record->shop]) :
                        null),
                ]),
                Section::make('Category')->schema([
                    TextEntry::make('subcategory.category.name')->url(fn (Product $record) => $record->subcategory ?
                        CategoryResource::getUrl('view', ['record' => $record->subcategory->category]) :
                        null)->label('Category'),
                    TextEntry::make('subcategory.name')->url(fn (Product $record) => $record->subcategory ?
                        SubCategoryResource::getUrl('view', ['record' => $record->subcategory]) :
                        null)->label('Subcategory'),
                ])->columns(2),
                Section::make('Basic Information')->schema([
                    TextEntry::make('name'),
                    TextEntry::make('slug'),
                    TextEntry::make('description')
                        ->columnSpanFull(),
                ]),
                Section::make('Pricing')->schema([
                    TextEntry::make('price')
                        ->money(),
                    TextEntry::make('shipping_price')
                        ->money(),
                    TextEntry::make('stock')
                        ->numeric(),
                    TextEntry::make('low_stock_threshold')
                        ->numeric(),
                    TextEntry::make('discount_type'),
                    TextEntry::make('discount_value')
                        ->numeric()
                        ->placeholder('-'),
                ])->columns(2),
                Section::make('Visibility')->schema([
                    IconEntry::make('show_in_app')
                        ->boolean(),
                    IconEntry::make('is_featured')
                        ->boolean(),
                    TextEntry::make('status'),
                    TextEntry::make('reviewed_by')
                        ->placeholder('-'),
                    TextEntry::make('reviewed_at')
                        ->dateTime()
                        ->placeholder('-'),
                    TextEntry::make('rejection_reason')
                        ->placeholder('-'),
                    TextEntry::make('created_at')
                        ->dateTime()
                        ->placeholder('-'),
                    TextEntry::make('updated_at')
                        ->dateTime()
                        ->placeholder('-'),
                ])->columns(2),
            ]);
    }
}
