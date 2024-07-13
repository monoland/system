<?php

namespace Module\System\Providers;

use Laravel\Sanctum\Sanctum;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Module\System\Models\SystemPersonalAccessToken;

class SystemServiceProvider extends ServiceProvider
{
    /**
     * Boot the application events.
     *
     * @return void
     */
    public function boot()
    {
        Gate::guessPolicyNamesUsing(function ($modelClass) {
            return str($modelClass)->before('\\Models\\')->toString() . '\\Policies\\' . str($modelClass)->after('\\Models\\')->toString() . 'Policy';
        });

        Sanctum::usePersonalAccessTokenModel(SystemPersonalAccessToken::class);

        $this->loadViewsFrom(
            __DIR__ . '/../../resources/views',
            'system'
        );

        $this->commands($this->discoverCommands());
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->register(EventServiceProvider::class);
        $this->app->register(RouteServiceProvider::class);
    }

    /**
     * The discoverCommands function
     *
     * @return array
     */
    protected function discoverCommands(): array
    {
        $commandPath = __DIR__ . '/../Commands';
        $commands = [];

        if (!$this->app->files->exists($commandPath)) {
            return $commands;
        }

        foreach($this->app->files->allFiles($commandPath) as $command) {
            $className = 'Module\\System\\Commands\\' . str($command->getBasename())->before('.php')->toString();

            if (class_exists($className)) {
                array_push($commands, $className);
            }
        }

        return $commands;
    }
}
