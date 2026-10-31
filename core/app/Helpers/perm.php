<?php
use App\Support\PermAbility;

if (! function_exists('can_route')) {
    function can_route(string $routeName, ?string $method = null): bool {
        $u = auth()->user();
        if (! $u || ! method_exists($u, 'canDo')) return false;
        $ability = PermAbility::inferByRoute($routeName, $method ?? request()->getMethod() ?? 'GET');
        return $u->canDo($routeName, $ability);
    }
}
