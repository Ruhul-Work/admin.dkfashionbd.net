<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckBranchSelected
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $branchId = current_branch_id();

        if (! $branchId) {
            return redirect()->route('backend.dashboard')
                ->with('error', 'Please select a branch');
        }

        return $next($request);
    }
}
