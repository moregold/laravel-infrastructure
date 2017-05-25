<?php namespace Moregold\Infrastructure;

use Illuminate\Support\ServiceProvider as LaravelServiceProvider;

class ServiceProvider extends LaravelServiceProvider {

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot() {

        $this->handleConfigs();
        // $this->handleMigrations();
        // $this->handleViews();
        // $this->handleTranslations();
        // $this->handleRoutes();
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register() {

        // Bind any implementations.

    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides() {

        return [];
    }

    private function handleConfigs() {

        $configPath = __DIR__ . '/../config/moregold-laravel-infrastructure.php';

        $this->publishes([$configPath => config_path('moregold-laravel-infrastructure.php')]);

        $this->mergeConfigFrom($configPath, 'moregold-laravel-infrastructure');
    }

    private function handleTranslations() {

        $this->loadTranslationsFrom(__DIR__.'/../lang', 'moregold-laravel-infrastructure');
    }

    private function handleViews() {

        $this->loadViewsFrom(__DIR__.'/../views', 'moregold-laravel-infrastructure');

        $this->publishes([__DIR__.'/../views' => base_path('resources/views/vendor/moregold-laravel-infrastructure')]);
    }

    private function handleMigrations() {

        $this->publishes([__DIR__ . '/../migrations' => base_path('database/migrations')]);
    }

    private function handleRoutes() {

        include __DIR__.'/../routes.php';
    }
}
