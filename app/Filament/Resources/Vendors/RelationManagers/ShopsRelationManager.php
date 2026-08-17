<?php

namespace App\Filament\Resources\Vendors\RelationManagers;

use App\Filament\Resources\Shops\ShopResource;
use App\Filament\Resources\Shops\Tables\ShopsTable;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;

class ShopsRelationManager extends RelationManager
{
    protected static string $relationship = 'shops';

    protected static ?string $relatedResource = ShopResource::class;

    public function table(Table $table): Table
    {
        return ShopsTable::configure($table);
    }
}
