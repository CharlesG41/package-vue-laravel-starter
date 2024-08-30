<?php

namespace Cyvian\Src\app\Repositories;

use Cyvian\Src\App\Models\Cyvian\Locale as EloquentLocale;

class LocaleRepository
{
    public function getLocalesByType(string $isType)
    {
        return EloquentLocale::where($isType, true)->get();
    }

    public function getLocaleByCode(string $localeCode)
    {
        return EloquentLocale::where('code', $localeCode)->get()->first();
    }
}
