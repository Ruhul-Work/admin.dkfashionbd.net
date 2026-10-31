<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class PosFirewall
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */

    public function handle(Request $request, Closure $next): Response
    {
        // Normalize IP
        $ip = $request->ip() ?: 'unknown';

        $ips = [$ip];
        if ($ip === '127.0.0.1') {
            $ips[] = '::1';
        }

        if ($ip === '::1') {
            $ips[] = '127.0.0.1';
        }

        // Super admin logged-in ⇒ always allow
        if ($request->user()?->isSuper()) {
            return $next($request);
        }

        // Route detect (with fallback)
        $isLoginGet = $request->routeIs('backend.login')
        || $request->is('backend/login');

        $isLoginPost = $request->routeIs('backend.login.action')
            || ($request->is('backend/login') && $request->isMethod('post'));

        // Dual form safety (127.x & ::1)
        $ips = [$ip];
        if ($ip === '127.0.0.1') {
            $ips[] = '::1';
        }

        // Allowlist (short TTL)
        $allow = Cache::remember("fw:allow:$ip", 60, fn() =>
            DB::table('firewall_rules')->whereNull('deleted_at')
                ->whereIn('ip_address', $ips)->where('type', 'allow')->exists()
        );
        if ($allow) {
            return $next($request);
        }

        // Blocklist (short TTL)
        $blocked = Cache::remember("fw:block:$ip", 60, fn() =>
            DB::table('firewall_rules')->whereNull('deleted_at')
                ->whereIn('ip_address', $ips)->where('type', 'block')->exists()
        );

        if ($blocked) {
            // JSON/AJAX
            if ($request->expectsJson()) {
                return response()->json(['message' => 'IP blocked'], 403)
                    ->header('X-Blocked-By', 'AppFirewall')
                    ->header('X-Reason', 'BlockedIP');
            }

            // Login GET: show page with banner
            if ($isLoginGet && $request->isMethod('get')) {
                $msg = 'Too many failed attempts from your IP. Access is blocked.';
                $request->session()->now('fw_block_msg', $msg);
                $request->session()->flash('fw_block_msg', $msg);
                $response = $next($request);
                $response->headers->set('X-FW-Debug', 'login-get-banner');
                return $response;
            }

            // Login POST: Super admin can log in & redirect to login with flash

            if ($isLoginPost) {
                $identifier = Str::lower((string) ($request->input('identifier') ?? $request->input('email') ?? $request->input('username') ?? $request->input('phone') ?? ''));
                if ($identifier !== '') {
                    $isTargetSuper = DB::table('users')
                        ->leftJoin('roles', 'roles.id', '=', 'users.role_id')
                        ->where(function ($q) use ($identifier) {
                            $q->where('users.email', $identifier)
                                ->orWhere('users.username', $identifier)
                                ->orWhere('users.phone', $identifier);
                        })
                        ->value('roles.is_super');
                    if ($isTargetSuper) {
                        return $next($request); 
                    }
                }
            }

            if ($isLoginPost && $request->isMethod('post')) {
                return redirect()
                    ->route('backend.login')
                    ->with('fw_block_msg', 'Too many failed attempts from your IP. Access is blocked.');
            }

            // Other backend routes: 403
            return response('Forbidden', 403)
                ->header('X-Blocked-By', 'AppFirewall')
                ->header('X-Reason', 'BlockedIP');
        }

        return $next($request);
    }

}
