<?php

namespace Charlesg\Cms;

use Charlesg\Cms\App\Http\Middleware\SetLocaleMiddleware;
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
            __DIR__.'/resources/views' => resource_path('views/charlesg-cms'),
            __DIR__.'/resources/lang' => resource_path('lang/charlesg-cms'),
        ], 'charlesg-cms-base');

        $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'charlesg-cms');
    }
}