<?php

namespace App\Filament\Resources\PaymentLogs;

use App\Filament\Resources\PaymentLogs\Pages\CreatePaymentLog;
use App\Filament\Resources\PaymentLogs\Pages\EditPaymentLog;
use App\Filament\Resources\PaymentLogs\Pages\ListPaymentLogs;
use App\Filament\Resources\PaymentLogs\Pages\ViewPaymentLog;
use App\Filament\Resources\PaymentLogs\Schemas\PaymentLogForm;
use App\Filament\Resources\PaymentLogs\Schemas\PaymentLogInfolist;
use App\Filament\Resources\PaymentLogs\Tables\PaymentLogsTable;
use App\Models\PaymentLog;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class PaymentLogResource extends Resource
{
    protected static ?string $model = PaymentLog::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return PaymentLogForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return PaymentLogInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PaymentLogsTable::configure($table);
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
            'index' => ListPaymentLogs::route('/'),
            'create' => CreatePaymentLog::route('/create'),
            'view' => ViewPaymentLog::route('/{record}'),
            'edit' => EditPaymentLog::route('/{record}/edit'),
        ];
    }
}
