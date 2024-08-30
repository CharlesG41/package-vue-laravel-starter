<?php

namespace Cyvian\Src\App\Utils;

use stdClass;

class LocaleHelper
{
    static public function mapTranslation(string $key, array $replaces = []): array
    {
        $locales = config('locales.locales');
        
        $translations = [];
        foreach ($locales as $locale) {
            $translations[$locale->code] = __($key, $replaces, $locale->code);
        }

        return $translations;
    }
}
