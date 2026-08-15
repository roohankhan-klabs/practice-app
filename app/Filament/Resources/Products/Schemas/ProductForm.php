<?php

namespace App\Filament\Resources\Products\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('shop_id')
                    ->required()
                    ->numeric(),
                TextInput::make('sub_category_id')
                    ->required()
                    ->numeric(),
                TextInput::make('name')
                    ->required(),
                TextInput::make('slug')
                    ->required(),
                Textarea::make('description')
                    ->required()
                    ->columnSpanFull(),
                TextInput::make('specifications')
                    ->required(),
                TextInput::make('price')
                    ->required()
                    ->numeric()
                    ->prefix('$'),
                TextInput::make('shipping_price')
                    ->required()
                    ->numeric()
                    ->default(0)
                    ->prefix('$'),
                TextInput::make('stock')
                    ->required()
                    ->numeric(),
                TextInput::make('low_stock_threshold')
                    ->required()
                    ->numeric()
                    ->default(5),
                TextInput::make('discount_type')
                    ->required()
                    ->default('percentage'),
                TextInput::make('discount_value')
                    ->numeric(),
                Toggle::make('show_in_app')
                    ->required(),
                Toggle::make('is_featured')
                    ->required(),
                TextInput::make('status')
                    ->required()
                    ->default('pending'),
                TextInput::make('reviewed_by'),
                DateTimePicker::make('reviewed_at'),
                TextInput::make('rejection_reason'),
            ]);
    }
}
