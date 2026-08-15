<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(
    ['name', 'code', 'iso_code', 'phone_code', 'phone_number_digits', 'country_code', 'currency', 'currency_code', 'currency_symbol', 'currency_exchange_rate', 'currency_exchange_rate_date']
)]
class Country extends Model
{
    public function states()
    {
        return $this->hasMany(State::class);
    }

    public function cities()
    {
        return $this->hasMany(City::class);
    }

    public function addresses()
    {
        return $this->hasMany(Address::class);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function shops()
    {
        return $this->hasMany(Shop::class);
    }

    public function shop_applications()
    {
        return $this->hasMany(ShopApplication::class);
    }
}
