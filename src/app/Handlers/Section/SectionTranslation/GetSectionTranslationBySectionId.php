<?php

namespace Cyvian\Src\app\Handlers\Section\SectionTranslation;

use Cyvian\Src\app\Classes\Locale;
use Cyvian\Src\app\Classes\Translations\SectionTranslation;
use Cyvian\Src\App\Handlers\Locale\GetLocalesByType;
use Cyvian\Src\app\Repositories\LocaleRepository;
use Cyvian\Src\app\Repositories\SectionTranslationRepository;
use Cyvian\Src\app\Utils\Localisation;

class GetSectionTranslationBySectionId
{
    private $sectionTranslationRepository;
    private $localeRepository;

    public function __construct(
        SectionTranslationRepository $sectionTranslationRepository,
        LocaleRepository $localeRepository
    )
    {
        $this->sectionTranslationRepository = $sectionTranslationRepository;
        $this->localeRepository = $localeRepository;
    }


    public function handle(int $sectionId)
    {
        $eloquentSectionTranslations = $this->sectionTranslationRepository->getSectionTranslationsBySectionId($sectionId);
        $getSiteLocales = new GetLocalesByType($this->localeRepository);
        $locales = $getSiteLocales->handle(Locale::IS_SITE);
        $labels = [];

        foreach($locales as $locale) {
            $labels[$locale->code] = $eloquentSectionTranslations->where('locale_id', $locale->id)->first()->label ?? '';
        }

        $sectionTranslation = new SectionTranslation(
            new Localisation($labels)
        );

        $sectionTranslation->setSectionId($sectionId);

        return $sectionTranslation;
    }
}
