<?php
namespace App\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use App\Models\backend\CompanySetting;
use App\Models\backend\WebsiteSetting;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Blade::if('perm', fn($keyOrRoute, $ability = 'view') =>
            auth()->check()
            && method_exists(auth()->user(), 'canDo')
            && auth()->user()->canDo($keyOrRoute, $ability)
        );

        // Multiple prefix supported: @permgroup(['usermanage.users.','usermanage.rbac.'])
        Blade::if('permgroup', function ($prefixes, array $abilities = null) {
            $u = auth()->user();
            if (! $u || ! method_exists($u, 'canAnyPrefix')) {
                return false;
            }

            $abilities = $abilities ?? ['view', 'add', 'edit', 'delete', 'export'];
            $prefixes  = is_array($prefixes) ? $prefixes : [$prefixes];

            foreach ($prefixes as $p) {
                if ($u->canAnyPrefix(rtrim($p, '.'), $abilities)) { // ⬅️ এখানে rtrim
                    return true;
                }
            }
            return false;
        });

        // Share active company setting with all views (logo, name, phone etc.)
        try {
            $companySettings = null;
            if (class_exists(WebsiteSetting::class)) {
                $companySettings = WebsiteSetting::first();
            }
            view()->share('companySettings', $companySettings);
        } catch (\Exception $e) {
            // Ignore DB errors during install/migrations
        }

        //froce https in production
        //     if (request()->header('x-forwarded-proto') == 'https') {
        // \URL::forceScheme('https');
    // }
    }


}
