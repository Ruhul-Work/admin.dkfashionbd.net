<?php
namespace App\Support;

class PermAbility
{
    public static function inferByRoute(string $routeName, string $method = 'GET'): string
    {
        if (preg_match('/\.(index|list|grid|show)$/', $routeName)) return 'view';
        if (preg_match('/\.(create|store)$/', $routeName))         return 'add';
        if (preg_match('/\.(edit|update)$/', $routeName))          return 'edit';
        if (preg_match('/\.(destroy|delete)$/', $routeName))       return 'delete';
        if (str_contains($routeName, 'export'))                    return 'export';

        return match (strtoupper($method)) {
            'GET' => 'view', 'POST' => 'add',
            'PUT','PATCH' => 'edit', 'DELETE' => 'delete',
            default => 'view',
        };
    }
}
