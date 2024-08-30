<?php

namespace Charlesg\Cms;

use Genl\Matice\MaticeServiceProvider;
use Illuminate\Support\Facades\File;
use Illuminate\Support\ServiceProvider;
use Charlesg\Cms\app\Services\TranslationService;

class CmsServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton('cms-translations', function ($app) {
            return new TranslationService();
        });
    }

    public function boot()
    {
        $this->loadRoutesFrom(__DIR__ . '/routes/web.php');

        // js, assets
        $this->publishes([
            __DIR__.'/../dist' => public_path('vendor/charlesg-cms'),
        ], 'charlesg-cms-assets');
        

        // translations
        $this->publishes([
            __DIR__.'/resources/lang' => resource_path('lang/charlesg'),
        ], 'charlesg-cms-translations');

        $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'charlesg');

         // Register the middleware
         $this->app['router']->aliasMiddleware('set-locale', SetLocaleMiddleware::class);

         // Apply the middleware to all web routes
         $this->app['router']->pushMiddlewareToGroup('web', 'set-locale');
    }
}