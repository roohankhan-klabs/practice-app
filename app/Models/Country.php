<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(
    ['name', 'code', 'iso_code', 'phone_code', 'phone_number_digits', 'country_code', 'currency', 'currency_code', 'currency_symbol', 'currency_exchange_rate', 'currency_exchange_rate_date']
)]
class Country extends Model
{
    public function addresses()
    {
        return $this->hasMany(Address::class);
    }
}
