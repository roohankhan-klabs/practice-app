<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $user_id
 * @property int $device_id
 */
#[Fillable(
    [
        'user_id',
        'device_id',
    ]
)]
class DeviceUser extends Model
{
    protected $table = 'device_users';

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function device()
    {
        return $this->belongsTo(Device::class);
    }
}
