<?php

namespace App\Filament\Resources\ShopApplications;

use App\Filament\Resources\ShopApplications\Pages\CreateShopApplication;
use App\Filament\Resources\ShopApplications\Pages\EditShopApplication;
use App\Filament\Resources\ShopApplications\Pages\ListShopApplications;
use App\Filament\Resources\ShopApplications\Pages\ViewShopApplication;
use App\Filament\Resources\ShopApplications\Schemas\ShopApplicationForm;
use App\Filament\Resources\ShopApplications\Schemas\ShopApplicationInfolist;
use App\Filament\Resources\ShopApplications\Tables\ShopApplicationsTable;
use App\Models\ShopApplication;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class ShopApplicationResource extends Resource
{
    protected static ?string $model = ShopApplication::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::DocumentChartBar;
    protected static string|UnitEnum|null $navigationGroup = 'Shops';
    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return ShopApplicationForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ShopApplicationInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ShopApplicationsTable::configure($table);
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
            'index' => ListShopApplications::route('/'),
            'create' => CreateShopApplication::route('/create'),
            'view' => ViewShopApplication::route('/{record}'),
            'edit' => EditShopApplication::route('/{record}/edit'),
        ];
    }
}
