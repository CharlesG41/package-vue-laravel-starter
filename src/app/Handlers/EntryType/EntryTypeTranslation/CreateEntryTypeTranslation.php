<?php

namespace Cyvian\Src\app\Handlers\EntryType\EntryTypeTranslation;

use Cyvian\Src\app\Classes\Locale;
use Cyvian\Src\App\Handlers\Locale\GetLocalesByType;
use Cyvian\Src\app\Repositories\EntryTypeTranslationRepository;
use Cyvian\Src\App\Repositories\LocaleRepository;
use Cyvian\Src\App\Utils\Localisation;

class CreateEntryTypeTranslation
{
    private $entryTypeTranslationRepository;

    public function __construct(EntryTypeTranslationRepository $entryTypeTranslationRepository)
    {
        $this->entryTypeTranslationRepository = $entryTypeTranslationRepository;
    }

    public function handle(Localisation $singularName, Localisation $pluralName, int $parentId)
    {
        $getLocalesByType = new GetLocalesByType(new LocaleRepository);
        $locales = $getLocalesByType->handle(Locale::IS_CMS);

        foreach($locales as $locale) {
            $this->entryTypeTranslationRepository->createEntryTypeTranslation(
                $singularName->{$locale->code},
                $pluralName->{$locale->code},
                $parentId,
                $locale->id
            );
        }
    }
}
