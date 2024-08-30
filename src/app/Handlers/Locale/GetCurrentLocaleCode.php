<?php

namespace Cyvian\Src\app\Handlers\Locale;

class GetCurrentLocaleCode
{
    public function handle(): string
    {
        return app()->getLocale() ?? 'en';
    }
}
