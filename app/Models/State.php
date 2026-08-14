<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['name', 'country_id'])]
class State extends Model
{
    public function country()
    {
        return $this->belongsTo(Country::class);
    }
}
