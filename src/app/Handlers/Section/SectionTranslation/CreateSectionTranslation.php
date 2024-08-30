<?php

namespace Cyvian\Src\app\Handlers\Section\SectionTranslation;

use Cyvian\Src\app\Classes\Locale;
use Cyvian\Src\app\Classes\Translations\SectionTranslation;
use Cyvian\Src\App\Handlers\Locale\GetLocalesByType;
use Cyvian\Src\app\Repositories\LocaleRepository;
use Cyvian\Src\app\Repositories\SectionTranslationRepository;

class CreateSectionTranslation
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

    public function handle(SectionTranslation $sectionTranslation)
    {
        $getLocalesByType = new GetLocalesByType($this->localeRepository);
        $locales = $getLocalesByType->handle(Locale::IS_CMS);

        foreach($locales as $locale) {
            $this->sectionTranslationRepository->createSectionTranslation(
                $sectionTranslation->labels->{$locale->code},
                $sectionTranslation->sectionId,
                $locale->id
            );
        }
    }
}
