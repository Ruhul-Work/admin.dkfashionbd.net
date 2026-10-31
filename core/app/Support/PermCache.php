<?php

namespace App\Support;

use Illuminate\Support\Facades\Cache;

class PermCache
{
    public static function forgetMaps(): void
    {
        Cache::forget('perm_map');
        Cache::forget('perm_key_map');
        Cache::forget('perm_id_by_key');
    }

    public static function forgetRole(int $roleId): void
    {
        Cache::forget("perm_role_{$roleId}");
    }

    public static function forgetUser(int $userId): void
    {
        Cache::forget("perm_user_{$userId}");
    }
}
