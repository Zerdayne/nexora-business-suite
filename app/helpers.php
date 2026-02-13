<?php

use App\Tenancy\TenantContext;
use App\Tenancy\TenantContextManager;

function tenant(): TenantContext
{
    return app(TenantContextManager::class)->require();
}
