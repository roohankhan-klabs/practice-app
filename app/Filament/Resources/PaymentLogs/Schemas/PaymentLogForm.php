<?php

namespace App\Filament\Resources\PaymentLogs\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class PaymentLogForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('payment_id')
                    ->required()
                    ->numeric(),
                TextInput::make('order_id')
                    ->required()
                    ->numeric(),
                TextInput::make('gateway')
                    ->required(),
                TextInput::make('charge_id')
                    ->required(),
                TextInput::make('event_type')
                    ->required(),
                TextInput::make('amount')
                    ->required()
                    ->numeric(),
                TextInput::make('currency')
                    ->required(),
                TextInput::make('status')
                    ->required(),
                TextInput::make('payload')
                    ->required(),
                TextInput::make('response')
                    ->required(),
                TextInput::make('ip_address'),
                DateTimePicker::make('paid_at'),
            ]);
    }
}
