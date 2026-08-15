<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(
    [
        'variant_type_id',
        'name',
        'value',
        'hex_code',
        'is_active',
    ]
)]
class VariantOption extends Model
{
    public function variantType()
    {
        return $this->belongsTo(VariantType::class);
    }

    public function variants()
    {
        return $this->belongsToMany(Variant::class, 'variant_option_ids');
    }
}
