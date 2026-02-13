<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tenant extends Model
{
    use HasUuids;
    use SoftDeletes;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'slug',
        'name',
        'status',
        'timezone',
        'stripe_customer_id',
        'stripe_subscription_id',
        'plan_key',
        'billing_status',
        'entitlements_version',
    ];

    protected $casts = [
        'entitlements_version' => 'integer',
    ];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'tenant_users')
            ->withPivot(['status', 'invited_at', 'activated_at'])
            ->withTimestamps();
    }

    public function roles(): HasMany
    {
        return $this->hasMany(Role::class);
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    public function entitlements(): HasMany
    {
        return $this->hasMany(TenantEntitlement::class);
    }
}
