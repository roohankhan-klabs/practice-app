<?php

namespace App\Filament\Resources\Payments\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class PaymentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('user_id')
                    ->required()
                    ->numeric(),
                TextInput::make('payment_method_id')
                    ->required()
                    ->numeric(),
                TextInput::make('reference_type')
                    ->required(),
                TextInput::make('reference_id')
                    ->required(),
                TextInput::make('transaction_id')
                    ->required(),
                TextInput::make('tap_charge_id'),
                TextInput::make('tap_tracking_id'),
                TextInput::make('status')
                    ->required(),
                DateTimePicker::make('paid_at'),
            ]);
    }
}
