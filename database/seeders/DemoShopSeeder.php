<?php

namespace Database\Seeders;

use App\Models\Address;
use App\Models\City;
use App\Models\Shop;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DemoShopSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $vendors = User::query()
            ->whereIn('email', ['vendor1@gmail.com', 'vendor2@gmail.com'])
            ->get()
            ->keyBy('email');

        $shops = [
            [
                'vendor_email' => 'vendor1@gmail.com',
                'shop_name' => 'Alpha Gadgets',
                'description' => 'Consumer electronics and smart accessories.',
                'contact_number' => '03001234567',
                'whatsapp_number' => '03001234567',
                'country' => 'PK',
                'city' => 'Karachi',
                'state' => 'Sindh',
                'address_line_1' => '12 Tech Street',
                'address_line_2' => 'Floor 2',
                'postal_code' => '75500',
                'shipping_fee_type' => 'fixed',
                'shipping_fee_amount' => '250',
                'estimated_delivery_time' => '2-4 days',
                'commission_percentage' => '12',
            ],
            [
                'vendor_email' => 'vendor2@gmail.com',
                'shop_name' => 'Desert Living',
                'description' => 'Home, beauty, wellness, and travel essentials.',
                'contact_number' => '39440011',
                'whatsapp_number' => '39440011',
                'country' => 'BH',
                'city' => 'Manama',
                'state' => 'Capital Governorate',
                'address_line_1' => '88 Commerce Avenue',
                'address_line_2' => null,
                'postal_code' => '322',
                'shipping_fee_type' => 'percentage',
                'shipping_fee_amount' => '5',
                'estimated_delivery_time' => '1-3 days',
                'commission_percentage' => '10',
            ],
        ];

        foreach ($shops as $shopData) {
            $vendor = $vendors->get($shopData['vendor_email']);

            $shop = Shop::query()->updateOrCreate(
                ['shop_name' => $shopData['shop_name']],
                [
                    'user_id' => $vendor->id,
                    'description' => $shopData['description'],
                    'cover_image' => Str::slug($shopData['shop_name']).'-cover.jpg',
                    'logo' => Str::slug($shopData['shop_name']).'-logo.jpg',
                    'whatsapp_number' => $shopData['whatsapp_number'],
                    'contact_number' => $shopData['contact_number'],
                    'shipping_policy' => 'Orders ship within 24 hours of confirmation.',
                    'refund_policy' => 'Refunds are reviewed within 3 business days.',
                    'return_policy' => 'Returns accepted within 7 days for unopened items.',
                    'privacy_policy' => 'Customer data is handled under platform policy.',
                    'terms_of_service' => 'Orders are subject to stock availability.',
                    'google_maps_link' => 'https://maps.example.com/'.Str::slug($shopData['shop_name']),
                    'avg_rating' => '4.8',
                    'total_reviews' => '24',
                    'status' => 'active',
                    'is_featured' => '1',
                    'shipping_fee_type' => $shopData['shipping_fee_type'],
                    'shipping_fee_amount' => $shopData['shipping_fee_amount'],
                    'estimated_delivery_time' => $shopData['estimated_delivery_time'],
                    'commission_percentage' => $shopData['commission_percentage'],
                    'instagram' => '@'.Str::snake($shopData['shop_name']),
                    'facebook' => Str::headline($shopData['shop_name']),
                    'tiktok' => '@'.Str::slug($shopData['shop_name'], ''),
                ],
            );

            $address = $this->upsertAddress(
                shop: $shop,
                lineOne: $shopData['address_line_1'],
                lineTwo: $shopData['address_line_2'],
                postalCode: $shopData['postal_code'],
                countryCode: $shopData['country'],
                stateName: $shopData['state'],
                cityName: $shopData['city'],
                phone: $shopData['contact_number'],
            );

            $shop->forceFill(['address_id' => (string) $address->id])->save();
        }
    }

    private function upsertAddress(
        string $lineOne,
        ?string $lineTwo,
        string $postalCode,
        string $countryCode,
        string $stateName,
        string $cityName,
        string $phone,
        Shop $shop,
    ): Address {
        $city = City::query()
            ->where('name', $cityName)
            ->whereHas('state', fn ($query) => $query->where('name', $stateName))
            ->whereHas('country', fn ($query) => $query->where('code', $countryCode))
            ->firstOrFail();

        return Address::query()->updateOrCreate(
            [
                'user_id' => null,
                'shop_id' => $shop->id,
                'address_line_1' => $lineOne,
            ],
            [
                'address_line_2' => $lineTwo,
                'preffered_contact_number' => $phone,
                'postal_code' => $postalCode,
                'city_id' => $city->id,
                'state_id' => $city->state_id,
                'country_id' => $city->country_id,
                'is_default' => true,
            ],
        );
    }
}
