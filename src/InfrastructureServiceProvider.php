<?php namespace Moregold\Infrastructure;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class InfrastructureServiceProvider extends ServiceProvider
{
    public function register()
    {
        // $this->registerEvents();
        $this->app->bind('MessagesTypes', 'Moregold\Infrastructure\Messages\Types');
        $this->app->bind('ValidatorsKeys', 'Moregold\Infrastructure\Validators\Keys');
        $this->app->bind('Moregold\Infrastructure\Clients\HttpClientInterface', 'Moregold\Infrastructure\Clients\GuzzleHttpClient');
        $this->app->bind('Moregold\Infrastructure\Clients\User\Contracts\UserClientInterface', 'Moregold\Infrastructure\Clients\User\UserApiClient');
        $this->app->bind('Moregold\Infrastructure\Clients\Search\Contracts\SearchClientInterface', 'Moregold\Infrastructure\Clients\Search\SearchApiClient');
        $this->app->bind('Moregold\Infrastructure\Clients\Books\Contracts\BooksClientInterface', 'Moregold\Infrastructure\Clients\Books\BooksApiClient');
        $this->app->bind('Moregold\Infrastructure\Clients\Import\Contracts\ImportClientInterface', 'Moregold\Infrastructure\Clients\Import\ImportApiClient');
        $this->app->bind('Moregold\Infrastructure\Clients\Reporter\Contracts\ReporterClientInterface', 'Moregold\Infrastructure\Clients\Reporter\ReporterApiClient');
        $this->app->bind('Moregold\Infrastructure\Clients\Question\Contracts\QuestionsClientInterface', 'Moregold\Infrastructure\Clients\Question\QuestionsApiClient');
    }

    public function boot()
    {
//        $this->app->validator->resolver(function($translator, $data, $rules, $messages)
//        {
//            return new CustomRules($translator, $data, $rules, $messages);
//        });
        $this->handleConfigs();
        $this->handleMigrations();
        // $this->handleViews();
        // $this->handleTranslations();
        // $this->handleRoutes();
    }

    private function registerEvents()
    {
        Event::listen('eloquent.*', function ($model)
        {
            $firing = Event::firing();
            switch ($firing) {
                //'eloquent.saving: Moregold\User'
            }

            if (strpos($firing, 'eloquent.saving') !== false) {
                return $model->IsValid();
            }
        });
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
