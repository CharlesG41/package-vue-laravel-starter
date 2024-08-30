<?php

namespace Cyvian\Src\app\Handlers\EntryType\EntryTypeTranslation;

use Cyvian\Src\app\Classes\Locale;
use Cyvian\Src\app\Classes\Translations\ActionTranslation;
use Cyvian\Src\app\Classes\Translations\EntryTypeTranslation;
use Cyvian\Src\App\Handlers\Locale\GetLocalesByType;
use Cyvian\Src\app\Repositories\EntryTypeTranslationRepository;
use Cyvian\Src\app\Repositories\LocaleRepository;
use Cyvian\Src\app\Utils\Localisation;

class GetEntryTypeTranslationsByEntryTypeId
{
    private $entryTypeTranslationRepository;
    private $localeRepository;

    public function __construct(EntryTypeTranslationRepository $entryTypeTranslationRepository, LocaleRepository $localeRepository)
    {
        $this->entryTypeTranslationRepository = $entryTypeTranslationRepository;
        $this->localeRepository = $localeRepository;
    }

    public function handle(int $entryTypeId): EntryTypeTranslation
    {
        $getSiteLocales = new GetLocalesByType($this->localeRepository);

        $eloquentEntryTypeTranslations = $this->entryTypeTranslationRepository->getEntryTypeTranslationsByEntryTypeId($entryTypeId);
        if ($eloquentEntryTypeTranslations->isEmpty()) {
            throw new \Exception('Entry type translations not found');
        }

        $singularNames = [];
        $pluralNames = [];

        $locales = $getSiteLocales->handle(Locale::IS_SITE);

        foreach($locales as $locale) {
            $localisedEloquentEntryTypeTranslation = $eloquentEntryTypeTranslations->where('locale_id', $locale->id)->first();
            $singularNames[$locale->code] = $localisedEloquentEntryTypeTranslation->singular_name;
            $pluralNames[$locale->code] = $localisedEloquentEntryTypeTranslation->plural_name;
        }

        return new EntryTypeTranslation(
            new Localisation($singularNames),
            new Localisation($pluralNames),
        );
    }
}
