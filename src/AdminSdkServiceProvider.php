<?php

namespace SpursCloud\Admin;

use Illuminate\Support\ServiceProvider;

class AdminSdkServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/spurs-admin.php', 'spurs-admin');

        $this->app->singleton(AdminConfig::class, function ($app) {
            $c = $app['config']['spurs-admin'];

            return new AdminConfig(
                url: $c['url'],
                app: $c['app'] ?? null,
                secret: $c['secret'] ?? null,
                cacheTtl: (int) ($c['cache_ttl'] ?? 300),
                fallback: (bool) ($c['fallback'] ?? true),
            );
        });

        $this->app->alias(AdminConfig::class, 'spurs-admin');
    }

    public function boot(): void
    {
        $this->publishes([
            __DIR__.'/../config/spurs-admin.php' => config_path('spurs-admin.php'),
        ], 'spurs-admin-config');

        // Overlay admin-held values onto local Laravel config. Admin becomes the
        // source of truth for every mapped key it returns; anything it does not
        // return keeps the app's existing .env value (non-breaking).
        $map = (array) config('spurs-admin.map', []);
        if ($map) {
            /** @var AdminConfig $admin */
            $admin = $this->app->make(AdminConfig::class);
            $values = $admin->all();
            foreach ($map as $adminKey => $configPath) {
                if (array_key_exists($adminKey, $values)) {
                    config([$configPath => $values[$adminKey]]);
                }
            }
        }
    }
}
