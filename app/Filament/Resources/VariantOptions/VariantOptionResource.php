<?php

namespace App\Filament\Resources\VariantOptions;

use App\Filament\Resources\VariantOptions\Pages\CreateVariantOption;
use App\Filament\Resources\VariantOptions\Pages\EditVariantOption;
use App\Filament\Resources\VariantOptions\Pages\ListVariantOptions;
use App\Filament\Resources\VariantOptions\Pages\ViewVariantOption;
use App\Filament\Resources\VariantOptions\Schemas\VariantOptionForm;
use App\Filament\Resources\VariantOptions\Schemas\VariantOptionInfolist;
use App\Filament\Resources\VariantOptions\Tables\VariantOptionsTable;
use App\Models\VariantOption;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class VariantOptionResource extends Resource
{
    protected static ?string $model = VariantOption::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::Bars3;
    protected static string|UnitEnum|null $navigationGroup = 'Catalog';
    protected static ?int $navigationSort=4;

    public static function form(Schema $schema): Schema
    {
        return VariantOptionForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return VariantOptionInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return VariantOptionsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListVariantOptions::route('/'),
            'create' => CreateVariantOption::route('/create'),
            'view' => ViewVariantOption::route('/{record}'),
            'edit' => EditVariantOption::route('/{record}/edit'),
        ];
    }
}
