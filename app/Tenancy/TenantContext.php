<?php

namespace App\Tenancy;

final class TenantContext
{
    public function __construct(
        public readonly string $tenantId,
        public readonly string $slug,
        public readonly ?string $planKey,
        public readonly ?string $billingStatus,
        public readonly int $entitlementsVersion
    ) {}
}
