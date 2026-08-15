<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $shop_id
 * @property int $name
 */
#[Fillable(
    [
        'name',
        'description',
        'image',
        'sort_order',
        'status',
    ]
)]
class Category extends Model
{
    public const ACTIVE = 'active';

    public const INACTIVE = 'inactive';

    public function subcategories()
    {
        return $this->hasMany(SubCategory::class);
    }
}
