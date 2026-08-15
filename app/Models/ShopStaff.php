<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(
    [
        'user_id',
        'shop_id',
        'role_id',
        'employee_id',
        'is_active',
    ]
)]
class ShopStaff extends Model
{
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }
}
