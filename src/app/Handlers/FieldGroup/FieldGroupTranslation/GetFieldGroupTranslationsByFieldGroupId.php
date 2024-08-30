<?php

namespace Cyvian\Src\app\Handlers\FieldGroup\FieldGroupTranslation;

use Cyvian\Src\app\Classes\Locale;
use Cyvian\Src\app\Classes\Translations\ActionTranslation;
use Cyvian\Src\app\Classes\Translations\FieldGroupTranslation;
use Cyvian\Src\App\Handlers\Locale\GetLocalesByType;
use Cyvian\Src\app\Repositories\FieldGroupTranslationRepository;
use Cyvian\Src\app\Repositories\LocaleRepository;
use Cyvian\Src\app\Utils\Localisation;

class GetFieldGroupTranslationsByFieldGroupId
{
    private $fieldGroupTranslationRepository;
    private $localeRepository;

    public function __construct(
        FieldGroupTranslationRepository $fieldGroupTranslationRepository,
        LocaleRepository $localeRepository
    )
    {
        $this->fieldGroupTranslationRepository = $fieldGroupTranslationRepository;
        $this->localeRepository = $localeRepository;
    }

    public function handle(int $fieldGroupId): FieldGroupTranslation
    {
        $getLocalesByType = new GetLocalesByType($this->localeRepository);

        $eloquentFieldGroupTranslations = $this->fieldGroupTranslationRepository->getFieldGroupTranslationsByFieldGroupId($fieldGroupId);

        $names = [];

        $locales = $getLocalesByType->handle(Locale::IS_CMS);

        foreach($locales as $locale) {
            $names[$locale->code] = $eloquentFieldGroupTranslations->where('locale_id', $locale->id)->first()->name;
        }

        return new FieldGroupTranslation(
            new Localisation($names)
        );
    }
}
