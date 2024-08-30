<?php

namespace Cyvian\Src\App\Utils;

use Cyvian\Src\App\Models\Cyvian\Locale;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class Helper
{
    static public function snaketoCamel(string $string): string
    {
        return lcfirst(str_replace(' ', '', ucwords(str_replace('_', ' ', $string))));
    }

    static public function root(): string
    {
        return implode('/', explode('/', $_SERVER['DOCUMENT_ROOT'], -1));
    }

    // this should not be the way the cms is booted, it should be with CmsServiceProvider
    static public function bootCyvianCms()
    {
        Schema::defaultStringLength(191);
        if (Schema::hasTable('locales')) {
            Config::set('locales.site', Locale::all()->mapWithKeys(function ($locale) {
                return [$locale->code => strtoupper($locale->code)];
            })->toArray());
            Config::set('app.locale', session('locale', 'fr'));
            Config::set('locales.current_locale', Locale::where('code', session('locale', 'fr'))->get()->first());
            Config::set('locales.locales', Locale::all());
            Config::set('locales.locales_cms', Locale::all());
        }

        if (env('APP_DEBUG')) {
            DB::enableQueryLog();
        }
    }

    static public function getFieldClassFromType(string $type): string
    {
        return '\\Cyvian\\Src\\App\\Classes\\Fields\\' . ucfirst(self::snakeToCamel($type));
    }
}
