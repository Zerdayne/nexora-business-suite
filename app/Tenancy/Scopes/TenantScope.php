<?php

namespace App\Tenancy\Scopes;

use App\Tenancy\TenantContextManager;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

final class TenantScope implements Scope
{
    public function apply(Builder $builder, Model $model)
    {
        /** @var TenantContextManager $manager */
        $manager = app(TenantContextManager::class);

        $ctx = $manager->get();
        if (! $ctx) {
            return;
        } // @TODO: Optional -> throw exception?

        $builder->where($model->getTable().'.tenant_id', $ctx->tenantId);
    }
}
