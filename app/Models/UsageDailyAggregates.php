<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UsageDailyAggregates extends Model
{
    public $incrementing = false;

    public $timestamps = true;

    protected $table = 'usage_daily_aggregates';

    protected $fillable = [
        'tenant_id',
        'metric_key',
        'date',
        'value',
    ];

    protected $casts = [
        'date' => 'date',
        'value' => 'integer',
    ];
}
