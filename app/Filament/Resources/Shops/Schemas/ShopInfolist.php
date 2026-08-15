<?php

namespace App\Filament\Resources\Shops\Schemas;

use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class ShopInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('user_id')
                    ->numeric(),
                TextEntry::make('shop_name'),
                TextEntry::make('description')
                    ->placeholder('-')
                    ->columnSpanFull(),
                ImageEntry::make('cover_image')
                    ->placeholder('-'),
                TextEntry::make('logo')
                    ->placeholder('-'),
                TextEntry::make('whatsapp_number')
                    ->placeholder('-'),
                TextEntry::make('contact_number')
                    ->placeholder('-'),
                TextEntry::make('address_id')
                    ->placeholder('-'),
                TextEntry::make('shipping_policy')
                    ->placeholder('-'),
                TextEntry::make('refund_policy')
                    ->placeholder('-'),
                TextEntry::make('return_policy')
                    ->placeholder('-'),
                TextEntry::make('privacy_policy')
                    ->placeholder('-'),
                TextEntry::make('terms_of_service')
                    ->placeholder('-'),
                TextEntry::make('google_maps_link')
                    ->placeholder('-'),
                TextEntry::make('avg_rating')
                    ->placeholder('-'),
                TextEntry::make('total_reviews')
                    ->placeholder('-'),
                TextEntry::make('status')
                    ->placeholder('-'),
                TextEntry::make('is_featured')
                    ->placeholder('-'),
                TextEntry::make('shipping_fee_type')
                    ->placeholder('-'),
                TextEntry::make('shipping_fee_amount')
                    ->placeholder('-'),
                TextEntry::make('estimated_delivery_time')
                    ->placeholder('-'),
                TextEntry::make('commission_percentage')
                    ->placeholder('-'),
                TextEntry::make('instagram')
                    ->placeholder('-'),
                TextEntry::make('facebook')
                    ->placeholder('-'),
                TextEntry::make('tiktok')
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
