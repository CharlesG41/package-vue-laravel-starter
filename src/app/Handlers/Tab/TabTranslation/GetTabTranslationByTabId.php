<?php

namespace Cyvian\Src\app\Handlers\Tab\TabTranslation;

use Cyvian\Src\app\Classes\Locale;
use Cyvian\Src\app\Classes\Translations\TabTranslation;
use Cyvian\Src\App\Handlers\Locale\GetLocalesByType;
use Cyvian\Src\app\Repositories\LocaleRepository;
use Cyvian\Src\app\Repositories\TabTranslationRepository;
use Cyvian\Src\app\Utils\Localisation;

class GetTabTranslationByTabId
{
    private $tabTranslationRepository;
    private $localeRepository;

    public function __construct(TabTranslationRepository $tabTranslationRepository, LocaleRepository $localeRepository)
    {
        $this->tabTranslationRepository = $tabTranslationRepository;
        $this->localeRepository = $localeRepository;
    }

    public function handle(int $tabId): TabTranslation
    {
        $eloquentTabTranslations = $this->tabTranslationRepository->getTabTranslationsByTabId($tabId);
        $getSiteLocales = new GetLocalesByType($this->localeRepository);
        $locales = $getSiteLocales->handle(Locale::IS_SITE);
        $labels = [];

        foreach($locales as $locale) {
            $labels[$locale->code] = $eloquentTabTranslations->where('locale_id', $locale->id)->first()->label ?? '';
        }

        $tabTranslation = new TabTranslation(
            new Localisation($labels)
        );
        $tabTranslation->setTabId($tabId);

        return $tabTranslation;
    }
}
