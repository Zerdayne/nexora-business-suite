<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Feature extends Model
{
    use HasUuids;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'module_id',
        'key',
        'name',
        'type',
        'default_value',
    ];

    protected $casts = [
        'default_value' => 'array',
    ];

    public function module(): BelongsTo
    {
        return $this->belongsTo(Module::class);
    }

    public function plans(): BelongsToMany
    {
        return $this->belongsToMany(Plan::class, 'plan_entitlements')
            ->withPivot(['value'])
            ->withTimestamps();
    }
}
