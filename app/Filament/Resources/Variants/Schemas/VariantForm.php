<?php

namespace App\Filament\Resources\Variants\Schemas;

use App\Models\VariantOption;
use App\Models\VariantType;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class VariantForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Variant Details')
                    ->schema([
                        Select::make('product_id')
                            ->relationship('product', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),

                        Repeater::make('selected_options')
                            ->label('Variant Selections')
                            ->schema([
                                Select::make('variant_type_id')
                                    ->label('Variant Type')
                                    ->options(fn () => VariantType::query()
                                        ->where('is_active', true)
                                        ->orderBy('name')
                                        ->pluck('name', 'id')
                                        ->all())
                                    ->disableOptionsWhenSelectedInSiblingRepeaterItems()
                                    ->live()
                                    ->required()
                                    ->afterStateUpdated(fn (callable $set) => $set('variant_option_id', null)),

                                Select::make('variant_option_id')
                                    ->label('Option')
                                    ->options(fn (callable $get) => VariantOption::query()
                                        ->where('is_active', true)
                                        ->where('variant_type_id', $get('variant_type_id'))
                                        ->orderBy('name')
                                        ->pluck('name', 'id')
                                        ->all())
                                    ->searchable()
                                    ->preload()
                                    ->disabled(fn (callable $get) => blank($get('variant_type_id')))
                                    ->required(),
                            ])
                            ->addActionLabel('Add variant type')
                            ->columns(2)
                            ->defaultItems(1)
                            ->minItems(1)
                            ->columnSpanFull(),

                        TextInput::make('price')
                            ->required()
                            ->numeric()
                            ->prefix('$'),

                        TextInput::make('stock')
                            ->required()
                            ->numeric(),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
