<?php

namespace App\Tenancy;

use App\Models\Tenant;
use Illuminate\Http\Request;

final class TenantResolver {
    public function resolve(Request $request): ?Tenant
    {
        // 1) Header
        $headerTenantId = $request->header('X-Tenant-Id');
        if (!empty($headerTenantId))
            return Tenant::query()->whereKey($headerTenantId)->first();

        // 2) Subdomain: {tenant}.nexora.test
        $host = $request->getHost();
        $parts = explode('.', $host);
        if (count($parts) > 2) {
            $slug = $parts[0];
            return Tenant::query()->whereSlug($slug)->first();
        }

        return null;
    }
}
