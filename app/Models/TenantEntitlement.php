<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TenantEntitlement extends Model
{
    public $incrementing = false;

    protected $table = 'tenant_entitlements';

    protected $keyType = 'string';

    protected $fillable = [
        'tenant_id',
        'feature_key',
        'value',
        'version',
    ];

    protected $casts = [
        'value' => 'array',
        'version' => 'integer',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }
}
