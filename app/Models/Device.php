<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $device_id
 * @property string $fingerprint
 * @property string $app_version
 * @property string $device_os
 * @property string $device_os_version
 * @property string $device_type
 * @property string $device_name
 * @property string $device_manufacturer
 * @property bool $is_mobile
 * @property string $device_token
 * @property string $last_ip_address
 * @property Carbon $last_activity_at
 */
#[Fillable(
    [
        'device_id',
        'fingerprint',
        'app_version',
        'device_os',
        'device_os_version',
        'device_type',
        'device_name',
        'device_manufacturer',
        'is_mobile',
        'device_token',
        'last_ip_address',
        'last_activity_at',
    ]
)]
class Device extends Model {}
