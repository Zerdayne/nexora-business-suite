<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class UsageEvent extends Model
{
    use HasUuids;
    use SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'metric_key',
        'value',
        'occurred_at',
        'idempotency_key',
        'dimensions',
    ];

    protected $casts = [
        'value' => 'integer',
        'occurred_at' => 'datetime',
        'dimensions' => 'array',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }
}
