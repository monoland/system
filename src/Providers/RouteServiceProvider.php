<?php

namespace Module\System\Providers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * Called before routes are registered.
     *
     * Register any model bindings or pattern based filters.
     *
     * @return void
     */
    public function boot(): void
    {
        parent::boot();
    }

    /**
     * Define the routes for the application.
     *
     * @return void
     */
    public function map(): void
    {
        $this->mapApiRoutes();

        $this->mapWebRoutes();
    }

    /**
     * Define the "web" routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     *
     * @return void
     */
    protected function mapWebRoutes(): void
    {
        Route::domain('backend.' . env('APP_URL'))
            ->middleware('web')
            ->prefix('account')
            ->namespace('Module\System\Http\Controllers')
            ->group(__DIR__ . '/../../routes/account-web.php');

        $domain = cache()->rememberForever('system-domain', function () {
            try {
                return optional(DB::table('system_modules')->where('slug', 'system')->first())->domain ?: 'backend';
            } catch (\Exception $e) {
                return 'backend';
            }
        });

        $prefix = cache()->rememberForever('system-prefix', function () {
            try {
                return optional(DB::table('system_modules')->where('slug', 'system')->first())->prefix ?: null;
            } catch (\Exception $e) {
                return null;
            }
        });

        Route::domain($domain . '.' . env('APP_URL'))
            ->middleware('web')
            ->prefix($prefix)
            ->namespace('Module\System\Http\Controllers')
            ->group(__DIR__ . '/../../routes/system-web.php');
    }

    /**
     * Define the "api" routes for the application.
     *
     * These routes are typically stateless.
     *
     * @return void
     */
    protected function mapApiRoutes(): void
    {
        Route::domain('backend.' . env('APP_URL'))
            ->prefix('account/api')
            ->middleware(['api', 'auth:sanctum'])
            ->namespace('Module\System\Http\Controllers')
            ->group(__DIR__ . '/../../routes/account-api.php');

        $domain = cache()->rememberForever('system-domain', function () {
            try {
                return optional(DB::table('system_modules')->where('slug', 'system')->first())->domain ?: 'backend';
            } catch (\Exception $e) {
                return 'backend';
            }
        });

        $prefix = cache()->rememberForever('system-prefix', function () {
            try {
                return optional(DB::table('system_modules')->where('slug', 'system')->first())->prefix ?: null;
            } catch (\Exception $e) {
                return null;
            }
        });

        Route::domain($domain . '.' . env('APP_URL'))
            ->prefix($prefix . '/api')
            ->middleware(['api', 'auth:sanctum'])
            ->namespace('Module\System\Http\Controllers')
            ->group(__DIR__ . '/../../routes/system-api.php');
    }
}
