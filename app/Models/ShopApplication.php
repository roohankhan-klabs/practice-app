<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(
    [
        'user_id',
        'shop_name',
        'email',
        'country_code',
        'phone',
        'vat_registration_number',
        'commercial_registration_number',
        'status',
        'rejection_reason',
    ]
)]
class ShopApplication extends Model
{
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }
}
