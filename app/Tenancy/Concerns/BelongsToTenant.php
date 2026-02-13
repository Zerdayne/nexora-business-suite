<?php

namespace App\Tenancy\Concerns;

use App\Tenancy\Scopes\TenantScope;
use App\Tenancy\TenantContextManager;

trait BelongsToTenant
{
    public static function bootBelongsToTenant(): void
    {
        static::addGlobalScope(new TenantScope);

        // TenantId automatisch setzen beim Creating (defense in depth)
        static::creating(function ($model) {
            if (! isset($model->tenant_id) || empty($model->tenant_id)) {
                /** @var TenantContextManager $manager */
                $manager = app(TenantContextManager::class);
                $ctx = $manager->require();
                $model->tenant_id = $ctx->tenantId;
            }
        });
    }
}
