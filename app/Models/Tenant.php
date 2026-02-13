<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tenant extends Model
{
    use HasUuids;
    use SoftDeletes;

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
}
