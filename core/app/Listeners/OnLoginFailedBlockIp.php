<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Failed;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OnLoginFailedBlockIp
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    const WINDOW_SECONDS = 600; // 10 মিনিটে...
    const THRESHOLD      = 4;   // ...৪ বার ফেল হলে ব্লক
 public function handle(Failed $event): void
    {
        $req = request();
        $ip  = $req->ip();
        if (!$ip) return;

        // কোন আইডেন্টিফায়ারটা ইউজড হয়েছে খুঁজে বের করা (event credentials/ request fallback)
        $creds = $event->credentials ?? [];
        $identifier = $creds['email']
            ?? $creds['username']
            ?? $creds['phone']
            ?? $req->input('email')
            ?? $req->input('username')
            ?? $req->input('phone')
            ?? $req->input('identifier')
            ?? '';

        $identifier = Str::lower((string) $identifier);

        // সুপার অ্যাডমিন টার্গেট হলে কখনও block নয়
        if ($identifier !== '') {
            $isSuper = DB::table('users')
                ->leftJoin('roles','roles.id','=','users.role_id')
                ->where(function($q) use ($identifier){
                    $q->where('users.email', $identifier)
                      ->orWhere('users.username', $identifier)
                      ->orWhere('users.phone', $identifier);
                })
                ->value('roles.is_super');

            if ($isSuper) return;
        }

        // ফেইল কাউন্টার
        $key   = "login:fail:ip:$ip";
        $count = Cache::add($key, 0, self::WINDOW_SECONDS) ? 0 : (int) Cache::get($key, 0);
        $count++;
        Cache::put($key, $count, self::WINDOW_SECONDS);

        // থ্রেশহোল্ড পেরোলে permanent block
        if ($count >= self::THRESHOLD) {
            DB::table('firewall_rules')->updateOrInsert(
                ['ip_address' => $ip],
                [
                    'type'       => 'block',
                    'comments'   => 'auto:login_fail_threshold',
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );
            Cache::forget("fw:block:$ip");
            Cache::forget("fw:allow:$ip");
            Cache::forget($key);
        }
    }
}
