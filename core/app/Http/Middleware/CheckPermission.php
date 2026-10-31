<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Support\PermAbility;

class CheckPermission
{
    public function handle(Request $request, Closure $next, string $ability = 'auto'): Response
    {
        $user = $request->user();
        $name = $request->route()?->getName(); // e.g. usermanage.users.index

        if (! $user || ! $name) {
            abort(403, 'Permission denied');
        }

        if ($ability === 'auto') {
            $ability = PermAbility::inferByRoute($name, $request->getMethod());
        }

        if (! $user->canDo($name, $ability)) {
            abort(403, 'Permission denied');
        }
        return $next($request);
    }
}

