<?php
namespace ME\Hr;

use Illuminate\Support\ServiceProvider;

class HrServiceProvider extends ServiceProvider
{
    public function boot()
    {
        if (file_exists(__DIR__ . '/routes/web.php')) {
            $this->loadRoutesFrom(__DIR__ . '/routes/web.php');
        }

        if (file_exists(__DIR__ . '/routes/api.php')) {
            $this->loadRoutesFrom(__DIR__ . '/routes/api.php');
        }

        $this->loadViewsFrom(__DIR__ . '/resources/views', 'hr');
        $this->loadTranslationsFrom(__DIR__ . '/resources/lang', 'hr');
        $this->loadMigrationsFrom(__DIR__ . '/database/migrations');

        $this->publishes([
            __DIR__ . '/Config' => config_path('hr'),
        ], 'hr-config');
    }

    public function register()
    {
        if (file_exists(__DIR__ . '/Config/config.php')) {
            $this->mergeConfigFrom(__DIR__ . '/Config/config.php', 'hr');
        }

        if (file_exists(__DIR__ . '/Config/sidebar.php')) {
            $this->mergeConfigFrom(__DIR__ . '/Config/sidebar.php', 'hr-sidebar');
        }

        if (file_exists(__DIR__ . '/Config/permission.php')) {
            $this->mergeConfigFrom(__DIR__ . '/Config/permission.php', 'hr-permission');
        }
    }
}
