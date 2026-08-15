<?php

namespace App\Filament\Resources\Payments\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class PaymentInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('user_id')
                    ->numeric(),
                TextEntry::make('payment_method_id')
                    ->numeric(),
                TextEntry::make('reference_type'),
                TextEntry::make('reference_id'),
                TextEntry::make('transaction_id'),
                TextEntry::make('tap_charge_id')
                    ->placeholder('-'),
                TextEntry::make('tap_tracking_id')
                    ->placeholder('-'),
                TextEntry::make('status'),
                TextEntry::make('paid_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
