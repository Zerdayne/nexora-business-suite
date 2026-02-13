<?php

namespace App\Entitlements;

use App\Models\Feature;
use App\Models\Module;
use App\Models\Plan;
use App\Models\Tenant;
use Illuminate\Support\Facades\DB;

final class EntitlementsRebuilder
{
    /**
     * Rebuilds tenant_entitlements for a given tenant based on:
     * - plan (tenants.plan_key -> plans)
     * - included modules from plan_modules
     * - active subscription modules (if present)
     * - feature defaults (features.default_value)
     * - plan_entitlements overrides
     * - tenant_entitlement_overrides overrides
     *
     * Returns new entitlements_version.
     */
    public function rebuildForTenant(Tenant $tenant): int
    {
        return DB::transaction(function () use ($tenant) {
            // Lock tenant row to avoid concurrent version bumps
            $tenant->lockForUpdate();

            $plan = $tenant->plan_key ? Plan::query()->where('key', $tenant->plan_key)->first() : null;

            $newVersion = $tenant->entitlements_version + 1;

            // 1) Determine active modules
            $activeModuleKeys = $this->resolveActiveModuleKeys($tenant, $plan);

            // 2) Build desired entitlements map: feature_key => value(array)
            $desired = [];

            // 2a) module enablement entitlements
            foreach (Module::query()->pluck('key')->all() as $moduleKey) {
                $desired["module.$moduleKey.enabled"] = ['enabled' => in_array($moduleKey, $activeModuleKeys, true)];
            }

            // 2b) system entitlements (limits from plan)
            if ($plan) {
                $desired['system.seats.included'] = ['limit' => (int) $plan->included_seats];
                $desired['system.api.units.included'] = ['limit' => (int) $plan->included_api_units];
                $desired['system.api.overage.allowed'] = ['enabled' => (bool) $plan->api_overage_allowed];
            }

            // 2c) feature entitlements for active modules
            $features = Feature::query()
                ->with('module:id,key')
                ->get(['id', 'module_id', 'key', 'type', 'default_value']);

            // Plan entitlements keyed by feature_id
            $planEntitlementsByFeatureId = [];
            if ($plan) {
                $rows = DB::table('plan_entitlements')
                    ->where('plan_id', $plan->id)
                    ->get(['feature_id', 'value']);

                foreach ($rows as $row) {
                    $planEntitlementsByFeatureId[$row->feature_id] = (array) json_decode($row->value, true);
                }
            }

            foreach ($features as $feature) {
                $moduleKey = $feature->module->key;

                if (! in_array($moduleKey, $activeModuleKeys, true)) {
                    // @TODO: Set disabled explicitly or omit
                    continue;
                }

                $fullKey = "$moduleKey.$feature->key"; // e.g. crm.export.csv

                // start from default_value (or empty)
                $value = is_array($feature->default_value) ? $feature->default_value : [];

                // apply plan entitlement override if present
                $planOverride = $planEntitlementsByFeatureId[$feature->id] ?? null;
                if (is_array($planOverride)) {
                    $value = array_replace_recursive($value, $planOverride);
                }

                $desired[$fullKey] = $value;
            }

            // 2d) tenant overrides (highest priority)
            $tenantOverrides = DB::table('tenant_entitlement_overrides')
                ->where('tenant_id', $tenant->id)
                ->get(['feature_key', 'value']);

            foreach ($tenantOverrides as $override) {
                $desired[$override->feature_key] = (array) json_decode($override->value, true);
            }

            // 3) Persist: upsert desired entitlements, delete stale ones, bump version
            $now = now();
            $rows = [];
            foreach ($desired as $featureKey => $value) {
                $rows[] = [
                    'tenant_id' => (string) $tenant->id,
                    'feature_key' => (string) $featureKey,
                    'value' => json_encode($value, JSON_UNESCAPED_SLASHES),
                    'version' => $newVersion,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }

            // Upsert by (tenant_id, feature_key)
            DB::table('tenant_entitlements')->upsert(
                $rows,
                ['tenant_id', 'feature_key'],
                ['value', 'version', 'updated_at']
            );

            // Delete stale entitlements that are no longer in desired set
            DB::table('tenant_entitlements')
                ->where('tenant_id', $tenant->id)
                ->whereNotIn('feature_key', array_keys($desired))
                ->delete();

            // Bump tenant version
            $tenant->entitlements_version = $newVersion;
            $tenant->save();

            return $newVersion;
        });
    }

    /**
     * Active modules come from:
     * - plan_modules where is_included = true
     * - subscription_modules active (if subscription snapshot exists)
     */
    private function resolveActiveModuleKeys(Tenant $tenant, ?Plan $plan): array
    {
        $active = [];

        if ($plan) {
            $included = DB::table('plan_modules')
                ->join('modules', 'modules.id', '=', 'plan_modules.module_id')
                ->where('plan_id', $plan->id)
                ->where('is_included', true)
                ->pluck('modules.key')
                ->all();

            $active = array_merge($active, $included);
        }

        $sub = DB::table('subscriptions')
            ->where('tenant_id', $tenant->id)
            ->orderByDesc('created_at')
            ->first();

        if ($sub) {
            $subModules = DB::table('subscription_modules')
                ->join('modules', 'modules.id', '=', 'subscription_modules.module_id')
                ->where('subscription_id', $sub->id)
                ->where('status', 'active')
                ->pluck('modules.key')
                ->all();

            $active = array_merge($active, $subModules);
        }

        // unique + keep only active modules in catalog
        $active = array_values(array_unique($active));

        return Module::query()
            ->whereIn('key', $active)
            ->where('is_active', true)
            ->pluck('key')
            ->all();
    }
}
