<?php

namespace Cyvian\Src\App\Handlers\Locale;

use Cyvian\Src\app\Classes\Locale;
use Cyvian\Src\app\Handlers\HandlerResponse;
use Cyvian\Src\App\Repositories\LocaleRepository;

class GetLocalesByType
{
    private $localeRepository;

    public function __construct(LocaleRepository $localeRepository)
    {
        $this->localeRepository = $localeRepository;
    }

    public function handle(string $isType): array
    {
        if ($isType != Locale::IS_SITE && $isType != Locale::IS_CMS) {
            throw new \InvalidArgumentException('Invalid locale type');
        }
        $eloquentLocales = $this->localeRepository->getLocalesByType($isType);
        $locales = [];
        foreach ($eloquentLocales as $locale) {
            $locales[] = new Locale(
                $locale->id,
                $locale->code,
                $locale->name,
                $locale->is_cms,
                $locale->is_site,
                $locale->is_default,
            );
        }

        return $locales;
    }
}
