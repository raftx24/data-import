<?php

namespace LaravelEnso\DataImport;

use Illuminate\Support\ServiceProvider;
use LaravelEnso\IO\app\Observers\IOObserver;
use LaravelEnso\DataImport\app\Models\DataImport;

class AppServiceProvider extends ServiceProvider
{
    public function boot()
    {
        DataImport::observe(IOObserver::class);

        $this->load()
            ->publish();
    }

    private function load()
    {
        $this->loadMigrationsFrom(__DIR__.'/database/migrations');

        $this->loadRoutesFrom(__DIR__.'/routes/api.php');

        $this->mergeConfigFrom(__DIR__.'/config/imports.php', 'imports');

        $this->loadViewsFrom(__DIR__.'/resources/views', 'laravel-enso/data-import');

        return $this;
    }

    private function publish()
    {
        $this->publishes([
            __DIR__.'/config' => config_path('enso'),
        ], 'data-import-config');

        $this->publishes([
            __DIR__.'/config' => config_path('enso'),
        ], 'enso-config');

        $this->publishes([
            __DIR__.'/database/factories' => database_path('factories'),
        ], 'data-import-factory');

        $this->publishes([
            __DIR__.'/database/factories' => database_path('factories'),
        ], 'enso-factories');

        $this->publishes([
            __DIR__.'/../resources' => app_path(),
        ], 'data-import-examples');

        $this->publishes([
            __DIR__.'/resources/views' => resource_path('views/vendor/laravel-enso/data-import'),
        ], 'data-import-mail');

        $this->publishes([
            __DIR__.'/resources/views' => resource_path('views/vendor/laravel-enso/data-import'),
        ], 'enso-mail');
    }
}
