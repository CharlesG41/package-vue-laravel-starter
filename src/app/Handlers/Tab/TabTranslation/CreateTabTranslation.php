<?php

namespace Cyvian\Src\app\Handlers\Tab\TabTranslation;

use Cyvian\Src\app\Classes\Locale;
use Cyvian\Src\app\Classes\Translations\TabTranslation;
use Cyvian\Src\App\Handlers\Locale\GetLocalesByType;
use Cyvian\Src\app\Repositories\TabTranslationRepository;
use Cyvian\Src\App\Repositories\LocaleRepository;

class CreateTabTranslation
{
    private $tabTranslationRepository;
    private $localeRepository;

    public function __construct(TabTranslationRepository $tabTranslationRepository, LocaleRepository $localeRepository)
    {
        $this->tabTranslationRepository = $tabTranslationRepository;
        $this->localeRepository = $localeRepository;
    }

    public function handle(TabTranslation $tabTranslation): void
    {
        $getLocalesByType = new GetLocalesByType($this->localeRepository);
        $locales = $getLocalesByType->handle(Locale::IS_CMS);

        foreach($locales as $locale) {
            $this->tabTranslationRepository->createTabTranslation(
                $tabTranslation->labels->{$locale->code},
                $tabTranslation->tabId,
                $locale->id
            );
        }
    }
}
