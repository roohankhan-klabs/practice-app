<?php

namespace App\Filament\Resources\Products\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Basic Information')
                    ->schema([
                        TextInput::make('name')
                            ->label('Product Name')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true),

                        Textarea::make('description')
                            ->required()
                            ->rows(5)
                            ->columnSpanFull(),

                        Textarea::make('specifications')
                            ->rows(5)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Section::make('Classification')
                    ->schema([
                        Select::make('shop_id')
                            ->label('Shop')
                            ->relationship('shop', 'shop_name')
                            ->searchable()
                            ->preload()
                            ->required(),

                        Select::make('sub_category_id')
                            ->label('Sub Category')
                            ->relationship('subcategory', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                    ])
                    ->columns(2),

                Section::make('Pricing')
                    ->schema([
                        TextInput::make('price')
                            ->required()
                            ->numeric()
                            ->minValue(0)
                            ->prefix('$'),

                        TextInput::make('shipping_price')
                            ->label('Shipping Price')
                            ->required()
                            ->numeric()
                            ->minValue(0)
                            ->default(0)
                            ->prefix('$'),

                        Select::make('discount_type')
                            ->options([
                                'percentage' => 'Percentage',
                                'fixed' => 'Fixed Amount',
                            ])
                            ->default('percentage')
                            ->required(),

                        TextInput::make('discount_value')
                            ->label('Discount')
                            ->numeric()
                            ->minValue(0)
                            ->default(0),
                    ])
                    ->columns(2),

                Section::make('Inventory')
                    ->schema([
                        TextInput::make('stock')
                            ->required()
                            ->numeric()
                            ->minValue(0),

                        TextInput::make('low_stock_threshold')
                            ->label('Low Stock Threshold')
                            ->required()
                            ->numeric()
                            ->minValue(0)
                            ->default(5),
                    ])
                    ->columns(2),

                Section::make('Visibility')
                    ->schema([
                        Toggle::make('show_in_app')
                            ->label('Show in App')
                            ->default(true),

                        Toggle::make('is_featured')
                            ->label('Featured Product')
                            ->default(false),

                        Select::make('status')
                            ->options([
                                'pending' => 'Pending',
                                'approved' => 'Approved',
                                'rejected' => 'Rejected',
                            ])
                            ->default('pending')
                            ->required(),
                    ])
                    ->columns(2)
            ]);
    }
}
