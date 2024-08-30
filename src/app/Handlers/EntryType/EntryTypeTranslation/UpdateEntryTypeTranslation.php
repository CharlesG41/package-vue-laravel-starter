<?php

namespace Cyvian\Src\app\Handlers\EntryType\EntryTypeTranslation;

use Cyvian\Src\app\Classes\Locale;
use Cyvian\Src\app\Classes\Translations\EntryTypeTranslation;
use Cyvian\Src\App\Handlers\Locale\GetCmsLocales;
use Cyvian\Src\App\Handlers\Locale\GetLocalesByType;
use Cyvian\Src\app\Repositories\EntryTypeTranslationRepository;
use Cyvian\Src\App\Repositories\LocaleRepository;
use Cyvian\Src\App\Utils\Localisation;

class UpdateEntryTypeTranslation
{
    private $entryTypeTranslationRepository;

    public function __construct(EntryTypeTranslationRepository $entryTypeTranslationRepository)
    {
        $this->entryTypeTranslationRepository = $entryTypeTranslationRepository;
    }

    public function handle(EntryTypeTranslation $entryTypeTranslation)
    {
        $getLocalesByType = new GetLocalesByType(new LocaleRepository);
        $locales = $getLocalesByType->handle(Locale::IS_CMS);

        foreach($locales as $locale) {
            $this->entryTypeTranslationRepository->updateEntryTypeTranslation(
                $entryTypeTranslation->id,
                $entryTypeTranslation->singularNames->{$locale->code},
                $entryTypeTranslation->pluralNames->{$locale->code},
                $locale->id
            );
        }
    }
}
