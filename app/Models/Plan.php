<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Plan extends Model
{
    use HasUuids;
    use SoftDeletes;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'key',
        'name',
        'included_seats',
        'included_api_units',
        'api_overage_allowed',
        'is_active',
    ];

    protected $casts = [
        'included_seats' => 'integer',
        'included_api_units' => 'integer',
        'api_overage_allowed' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function modules(): BelongsToMany
    {
        return $this->belongsToMany(Module::class, 'plan_modules')
            ->withPivot(['is_included'])
            ->withTimestamps();
    }
}
