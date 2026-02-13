<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TenantEntitlementOverride extends Model
{
    public $incrementing = false;

    protected $table = 'tenant_entitlement_overrides';

    protected $keyType = 'string';

    protected $fillable = [
        'tenant_id',
        'feature_key',
        'value',
        'reason',
    ];

    protected $casts = [
        'value' => 'array',
    ];
}
