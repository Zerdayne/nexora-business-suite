<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class TenantUser extends Pivot
{
    public $incrementing = false;

    public $timestamps = true;

    protected $table = 'tenant_users';

    protected $fillable = [
        'tenant_id',
        'user_id',
        'status',
        'invited_at',
        'activated_at',
    ];

    protected $casts = [
        'invited_at' => 'datetime',
        'activated_at' => 'datetime',
    ];
}
