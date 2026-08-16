<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'user_id',
    'phone',
    'email',
    'otp',
    'status',
])]
class Verification extends Model
{
    public const PENDING = 'pending';
    public const VERIFIED = 'verified';
    public const EXPIRED = 'expired';
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
