<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function tenants(): BelongsToMany
    {
        return $this->belongsToMany(Tenant::class, 'tenant_users')
            ->withPivot(['status', 'invited_at', 'activated_at'])
            ->withTimestamps();
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'user_roles')
            ->withPivot(['tenant_id'])
            ->withTimestamps();
    }

    public function hasPermission(string $permissionKey, string $tenantId): bool
    {
        $perms = $this->permissionsForTenant($tenantId);

        return in_array($permissionKey, $perms, true);
    }

    public function permissionsForTenant(string $tenantId): array
    {
        $cacheKey = "rbac:perms:$tenantId:$this->id";

        return Cache::store('redis')->remember($cacheKey, now()->addMinutes(10), function () use ($tenantId) {
            // Permissions via roles -> role_permissions -> permissions
            $rows = DB::table('user_roles')
                ->join('roles', 'roles.id', '=', 'user_roles.role_id')
                ->join('role_permissions', 'role_permissions.role_id', '=', 'roles.id')
                ->join('permissions', 'permissions.id', '=', 'role_permissions.permission_id')
                ->where('user_roles.tenant_id', $tenantId)
                ->where('user_roles.user_id', $this->id)
                ->select('permissions.key')
                ->distinct()
                ->pluck('key');

            return $rows->values()->all();
        });
    }

    public function flushRbacCache(string $tenantId): void
    {
        $cacheKey = "rbac:perms:$tenantId:$this->id";
        Cache::store('redis')->forget($cacheKey);
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
