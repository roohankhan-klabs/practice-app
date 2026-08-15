<?php

namespace App\Filament\Resources\Shops\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ShopForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('user_id')
                    ->required()
                    ->numeric(),
                TextInput::make('shop_name')
                    ->required(),
                Textarea::make('description')
                    ->columnSpanFull(),
                FileUpload::make('cover_image')
                    ->image(),
                TextInput::make('logo'),
                TextInput::make('whatsapp_number'),
                TextInput::make('contact_number'),
                TextInput::make('address_id'),
                TextInput::make('shipping_policy'),
                TextInput::make('refund_policy'),
                TextInput::make('return_policy'),
                TextInput::make('privacy_policy'),
                TextInput::make('terms_of_service'),
                TextInput::make('google_maps_link'),
                TextInput::make('avg_rating'),
                TextInput::make('total_reviews'),
                TextInput::make('status'),
                TextInput::make('is_featured'),
                TextInput::make('shipping_fee_type'),
                TextInput::make('shipping_fee_amount'),
                TextInput::make('estimated_delivery_time'),
                TextInput::make('commission_percentage'),
                TextInput::make('instagram'),
                TextInput::make('facebook'),
                TextInput::make('tiktok'),
            ]);
    }
}
