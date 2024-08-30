<?php

namespace Charlesg\Cms\App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class SetLocaleMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $userLanguage = $request->cookie('user_language');
        if ($userLanguage) {
            App::setLocale($userLanguage);
        }
        return $next($request);
    }
}