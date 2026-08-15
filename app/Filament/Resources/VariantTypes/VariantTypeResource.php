<?php

namespace App\Filament\Resources\VariantTypes;

use App\Filament\Resources\VariantTypes\Pages\CreateVariantType;
use App\Filament\Resources\VariantTypes\Pages\EditVariantType;
use App\Filament\Resources\VariantTypes\Pages\ListVariantTypes;
use App\Filament\Resources\VariantTypes\Pages\ViewVariantType;
use App\Filament\Resources\VariantTypes\Schemas\VariantTypeForm;
use App\Filament\Resources\VariantTypes\Schemas\VariantTypeInfolist;
use App\Filament\Resources\VariantTypes\Tables\VariantTypesTable;
use App\Models\VariantType;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class VariantTypeResource extends Resource
{
    protected static ?string $model = VariantType::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::RectangleGroup;

    protected static string|UnitEnum|null $navigationGroup = 'Catalog';

    public static function form(Schema $schema): Schema
    {
        return VariantTypeForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return VariantTypeInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return VariantTypesTable::configure($table);
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
            'index' => ListVariantTypes::route('/'),
            'create' => CreateVariantType::route('/create'),
            'view' => ViewVariantType::route('/{record}'),
            'edit' => EditVariantType::route('/{record}/edit'),
        ];
    }
}
