<?php

namespace Cyvian\Src\app\Handlers\Section\SectionTranslation;

use Cyvian\Src\app\Classes\Locale;
use Cyvian\Src\app\Classes\Translations\SectionTranslation;
use Cyvian\Src\App\Handlers\Locale\GetLocalesByType;
use Cyvian\Src\app\Repositories\LocaleRepository;
use Cyvian\Src\app\Repositories\SectionTranslationRepository;

class UpdateSectionTranslation
{
    private $sectionTranslationRepository;

    public function __construct(SectionTranslationRepository $sectionTranslationRepository)
    {
        $this->sectionTranslationRepository = $sectionTranslationRepository;
    }

    public function handle(SectionTranslation $sectionTranslation)
    {
        $getLocalesByType = new GetLocalesByType(new LocaleRepository);
        $locales = $getLocalesByType->handle(Locale::IS_CMS);

        foreach($locales as $locale) {
            $this->sectionTranslationRepository->updateSectionTranslation(
                $sectionTranslation->id,
                $sectionTranslation->labels->{$locale->code},
                $locale->id
            );
        }
    }
}
