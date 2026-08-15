<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $user_id
 * @property string $shop_name
 * @property string $description
 * @property string $address
 * @property string $phone
 * @property string $email
 * @property string $logo
 * @property string $cover_image
 * @property int $status
 */
#[Fillable(
    [
        'user_id',
        'shop_name',
        'description',
        'cover_image',
        'logo',
        'whatsapp_number',
        'contact_number',
        'address_id',
        'shipping_policy',
        'refund_policy',
        'return_policy',
        'privacy_policy',
        'terms_of_service',
        'google_maps_link',
        'avg_rating',
        'total_reviews',
        'status',
        'is_featured',
        'shipping_fee_type',
        'shipping_fee_amount',
        'estimated_delivery_time',
        'commission_percentage',
        'instagram',
        'facebook',
        'tiktok',
    ]
)]
class Shop extends Model
{
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function address()
    {
        return $this->belongsTo(Address::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
