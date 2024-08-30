<?php

namespace Cyvian\Src\app\Handlers\Locale;

use Cyvian\Src\app\Classes\Locale;
use Cyvian\Src\app\Repositories\LocaleRepository;

class GetLocalesAsArrayKeyCodeByType
{
    private $localeRepository;

    public function __construct(LocaleRepository $localeRepository)
    {
        $this->localeRepository = $localeRepository;
    }

    public function handle(string $isType): array
    {
        $getLocalesByType = new GetLocalesByType($this->localeRepository);

        $locales = $getLocalesByType->handle($isType);

        $localesByCode = [];
        foreach($locales as $locale) {
            $localesByCode[$locale->code] = $locale;
        }

        return $localesByCode;
    }
}
