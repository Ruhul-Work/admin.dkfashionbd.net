<?php

namespace App\Providers;


use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\Login;
use App\Listeners\OnLoginFailedBlockIp;
use App\Listeners\OnLoginSuccessResetCounter;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{

    protected $listen = [
        Failed::class => [OnLoginFailedBlockIp::class],
        Login::class  => [OnLoginSuccessResetCounter::class],
    ];
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
