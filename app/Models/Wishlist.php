<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;

/**
 * @property int $id
 * @property int $user_id
 * @property int $product_id
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 */
#[Fillable(['user_id', 'device_id', 'product_id'])]

class Wishlist extends Model
{
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function device()
    {
        return $this->belongsTo(DeviceUser::class);
    }
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
