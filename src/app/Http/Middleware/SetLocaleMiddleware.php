<?php

namespace Charlesg\App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;

class SetLocaleMiddleware
{
    public function handle($request, Closure $next)
    {
        $userLanguage = $request->cookie('user_language');

        if ($userLanguage) {
            App::setLocale($userLanguage);
        }
        return $next($request);
    }
}