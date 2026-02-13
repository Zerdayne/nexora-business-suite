<?php

namespace App\Features;

use App\Models\TenantEntitlement;
use App\Tenancy\TenantContextManager;
use Illuminate\Support\Facades\Cache;

final class FeatureGate
{
    public function __construct(private readonly TenantContextManager $manager) {}

    public function limit(string $featureKey): ?int
    {
        $value = $this->get($featureKey);
        if (! array_key_exists('limit', $value)) {
            return null;
        }

        return (int) $value['limit'];
    }

    public function get(string $featureKey): array
    {
        $snapshot = $this->snapshot();

        return $snapshot[$featureKey] ?? [];
    }

    /**
     * Returns snapshot: feature_key => value(array)
     * Cached under: ent:{tenantId}:{version}
     */
    public function snapshot(): array
    {
        $ctx = $this->manager->require();
        $cacheKey = "ent:$ctx->tenantId:$ctx->entitlementsVersion";

        return Cache::store('redis')->remember($cacheKey, now()->addMinutes(5), function () use ($ctx) {
            // only take latest rows for this tenant (version is written, but we can ignore it for read)
            $rows = TenantEntitlement::query()
                ->where('tenant_id', $ctx->tenantId)
                ->get(['feature_key', 'value']);

            $map = [];
            foreach ($rows as $row) {
                $map[$row->feature_key] = is_array($row->value) ? $row->value : [];
            }

            return $map;
        });
    }

    public function assertEnabled(string $featureKey, int $statusCode = 403): void
    {
        if ($this->enabled($featureKey)) {
            abort($statusCode, "Feature not enabled: $featureKey");
        }
    }

    public function enabled(string $featureKey): bool
    {
        $value = $this->get($featureKey);

        return (bool) ($value['enabled'] ?? false);
    }
}
