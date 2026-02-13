<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class SubscriptionModule extends Model
{
    use HasUuids;
    use SoftDeletes;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'subscription_id',
        'module_id',
        'status',
        'activated_at',
        'canceled_at',
    ];

    protected $casts = [
        'activated_at' => 'datetime',
        'canceled_at' => 'datetime',
    ];

    public function subscription(): BelongsTo
    {
        return $this->belongsTo(Subscription::class);
    }

    public function module(): BelongsTo
    {
        return $this->belongsTo(Module::class);
    }
}
