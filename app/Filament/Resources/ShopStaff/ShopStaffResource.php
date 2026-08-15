<?php

namespace App\Filament\Resources\ShopStaff;

use App\Filament\Resources\ShopStaff\Pages\CreateShopStaff;
use App\Filament\Resources\ShopStaff\Pages\EditShopStaff;
use App\Filament\Resources\ShopStaff\Pages\ListShopStaff;
use App\Filament\Resources\ShopStaff\Pages\ViewShopStaff;
use App\Filament\Resources\ShopStaff\Schemas\ShopStaffForm;
use App\Filament\Resources\ShopStaff\Schemas\ShopStaffInfolist;
use App\Filament\Resources\ShopStaff\Tables\ShopStaffTable;
use App\Models\ShopStaff;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ShopStaffResource extends Resource
{
    protected static ?string $model = ShopStaff::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return ShopStaffForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ShopStaffInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ShopStaffTable::configure($table);
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
            'index' => ListShopStaff::route('/'),
            'create' => CreateShopStaff::route('/create'),
            'view' => ViewShopStaff::route('/{record}'),
            'edit' => EditShopStaff::route('/{record}/edit'),
        ];
    }
}
