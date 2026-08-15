<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(
    [
        'review_id',
        'image',
    ]
)]
class ReviewImage extends Model
{
    public function review(){
        return $this->belongsTo(Review::class);
    }
}
