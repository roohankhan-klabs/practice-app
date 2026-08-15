<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['name'])]
class Role extends Model
{
    public const SUPER_ADMIN = 1;

    public const ADMIN = 2;

    public const CUSTOMER = 3;

    public const VENDOR = 4;

    public const STAFF = 5;

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
