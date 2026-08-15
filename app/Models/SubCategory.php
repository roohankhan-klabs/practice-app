<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $category_id
 * @property string $name
 * @property string $description
 * @property string $image
 * @property unsignedInteger $sort_order
 * @property string $status
 */

#[Fillable(
    [
        'category_id',
        'name',
        'description',
        'sort_order',
        'status',
    ]
)]

class SubCategory extends Model {
    public function category() {
        return $this->belongsTo(Category::class);
    }

    public function products() {
        return $this->hasMany(Product::class);
    }
}
