<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class UserRole extends Pivot
{
    public $incrementing = false;

    public $timestamps = true;

    protected $table = 'user_roles';

    protected $fillable = [
        'tenant_id',
        'user_id',
        'role_id',
    ];
}
