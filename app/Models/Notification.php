<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(
    [
        'user_id',
        'device_id',
        'type',
        'title',
        'data',
        'reference_type',
        'reference_id',
        'is_sent',
        'is_read',
        'sent_at',
        'read_at',
    ]
)]
class Notification extends Model
{
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function device()
    {
        return $this->belongsTo(Device::class);
    }
}
