<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['name'])]
class Status extends Model
{
    public const ACTIVE = 1;
    public const INACTIVE = 2;
    public const PENDING = 3;
    public const DELETED = 4;
    public const BLOCKED = 5;
    public const SUSPENDED = 6;
    public const EXPIRED = 7;

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
