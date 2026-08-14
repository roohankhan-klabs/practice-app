<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'user_id',
    'address_line_1',
    'address_line_2',
    'preffered_contact_number',
    'postal_code',
    'city_id',
    'state_id',
    'country_id',
    'is_default'
])]
class Address extends Model
{
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
