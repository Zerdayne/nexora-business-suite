<?php

namespace App\Tenancy;

use RuntimeException;

final class TenantContextManager
{
    private ?TenantContext $current = null;

    public function set(TenantContext $context): void
    {
        $this->current = $context;
    }

    public function get(): ?TenantContext
    {
        return $this->current;
    }

    public function require(): TenantContext
    {
        return $this->current ?? throw new RuntimeException('Tenant context is not set.');
    }
}
