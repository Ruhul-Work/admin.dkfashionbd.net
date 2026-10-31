<?php

namespace App\Http\Middleware;

use App\Support\BranchScope;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureBranchScope
{
    public function handle(Request $request, Closure $next): Response
    {
        BranchScope::ensureDefault();
        return $next($request);
    }
}
