<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StripePriceMapping extends Model
{
    use HasUuids;
    use SoftDeletes;

    public $incrementing = false;

    protected $keyType = 'string';

    // type: plan_base | plan_seat | plan_api_usage | module
    protected $fillable = [
        'type',
        'key',
        'stripe_product_id',
        'stripe_price_id',
    ];
}
