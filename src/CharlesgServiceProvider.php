<?php

namespace Charlesg;

use Illuminate\Support\ServiceProvider;
use Charlesg\app\Services\TranslationService;

class CharlesgServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton('charlesg-translations', function ($app) {
            return new TranslationService();
        });
    }

    public function boot()
    {
        $this->loadRoutesFrom(__DIR__ . '/routes/web.php');
        $this->loadViewsFrom(__DIR__ . '/resources/views', 'charlesg');

        $this->publishes([
            __DIR__.'/../dist' => public_path('vendor/charlesg'),
            __DIR__.'/resources/views' => resource_path('views/charlesg'),
            __DIR__.'/resources/lang' => resource_path('lang/charlesg'),
        ], 'charlesg-base');

        $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'charlesg');
    }
}