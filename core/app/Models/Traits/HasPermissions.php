<?php
namespace App\Models\Traits;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

trait HasPermissions
{
/** Super admin short-circuit */
    public function isSuper(): bool
    {
        return (bool)($this->role->is_super ?? false);
    }

    /** Public: check allow/deny by route name or permission key */
    public function canDo(string|int $keyOrRoute, string $ability = 'view'): bool
    {
        if ($this->isSuper()) return true;
        if (!$this->role_id) return false;

        $permId = $this->resolvePermissionId($keyOrRoute);
        if (!$permId) return false;

        $col = $this->abilityColumn($ability);

        // 1) User overrides (tri-state: NULL=inherit, 1=allow, 0=deny)
        $uPerms = $this->userPermCache();
        if (isset($uPerms[$permId])) {
            $val = $uPerms[$permId]->{$col};
            if ($val !== null) return (bool)$val;
        }

        // 2) Role baseline
        $rPerms = $this->rolePermCache();
        $row    = $rPerms[$permId] ?? null;
        return (bool)($row?->{$col} ?? false);
    }

    /** Menu heading auto-hide: any allowed inside this permission key prefix */

    public function canAnyPrefix(string $prefix, array $abilities = ['view','add','edit','delete','export']): bool
    {
        if ($this->isSuper()) return true;

        // normalize: 'usermanage.users.*' -> 'usermanage.users'
        if (str_ends_with($prefix, '.*')) $prefix = substr($prefix, 0, -2);
        $prefix = rtrim($prefix, '.');

        // সব permission key (cache) থেকে এই prefix-এর key গুলো নিন
        $idToKey = $this->permKeyMap(); // [id => 'usermanage.users.index', ...]
        $keys = [];
        foreach ($idToKey as $key) {
            if (Str::startsWith($key, $prefix.'.') || $key === $prefix) {
                $keys[] = $key;
            }
        }
        if (empty($keys)) return false;

        // প্রতি key-এ effective allow আছে কিনা দেখুন (override + role মিলিয়ে)
        foreach ($keys as $key) {
            $abs = $abilities ?: $this->inferAbilitiesFromKey($key);
            foreach ($abs as $ab) {
                if ($this->canDo($key, $ab)) {
                    return true; // কোনো একটা allow পেলেই group দেখান
                }
            }
        }
        return false; // একটাও effective allow না থাকলে group লুকান
    }

// key দেখে সম্ভাব্য ability ধরার helper (fallback সহ)
protected function inferAbilitiesFromKey(string $key): array
    {
        $k = strtolower($key);
        if (preg_match('/\.(index|list|grid|show)$/', $k))   return ['view'];
        if (preg_match('/\.(create|store)$/', $k))           return ['add'];
        if (preg_match('/\.(edit|update)$/', $k))            return ['edit'];
        if (preg_match('/\.(destroy|delete)$/', $k))         return ['delete'];
        if (str_contains($k, 'export'))                      return ['export'];
        // fallback: সব ability চেক করুন, যাতে কাস্টম key-ও কভার হয়
        return ['view','add','edit','delete','export'];
    }
    /* -------------------- helpers & caches -------------------- */

    /** resolve route name or permission key to permission_id */
    protected function resolvePermissionId(string|int $keyOrRoute): ?int
    {
        if (is_int($keyOrRoute)) return $keyOrRoute;

        // route → id
        $map = $this->permRouteMap(); // [route_name => permission_id]
        if (isset($map[$keyOrRoute])) return (int)$map[$keyOrRoute];

        // key → id
        $byKey = $this->permIdByKey(); // [key => id]
        return $byKey[$keyOrRoute] ?? null;
    }

    /** ability → column name */
    protected function abilityColumn(string $ability): string
    {
        return match (strtolower($ability)) {
            'view'   => 'can_view',
            'add'    => 'can_add',
            'edit'   => 'can_edit',
            'delete' => 'can_delete',
            'export' => 'can_export',
            default  => 'can_view',
        };
    }

    /** cache: role permissions keyed by permission_id */
    protected function rolePermCache()
    {
        return Cache::remember("perm_role_{$this->role_id}", 300, function () {
            return DB::table('role_permissions')
                ->where('role_id', $this->role_id)
                ->get()
                ->keyBy('permission_id');
        });
    }

    /** cache: user overrides keyed by permission_id */
    protected function userPermCache()
    {
        return Cache::remember("perm_user_{$this->id}", 300, function () {
            return DB::table('user_permissions')
                ->where('user_id', $this->id)
                ->get()
                ->keyBy('permission_id');
        });
    }

    /** cache: route_name → permission_id */
    protected function permRouteMap(): array
    {
        return Cache::remember('perm_map', 300, function () {
            return DB::table('permission_routes')->pluck('permission_id', 'route_name')->all();
        });
    }

    /** cache: permission_id → key */
    protected function permKeyMap(): array
    {
        return Cache::remember('perm_key_map', 300, function () {
            return DB::table('permissions')->pluck('key', 'id')->all();
        });
    }

    /** cache: key → id */
    protected function permIdByKey(): array
    {
        return Cache::remember('perm_id_by_key', 300, function () {
            return DB::table('permissions')->pluck('id', 'key')->all();
        });
    }
}
