<?php

namespace Cyvian\Src\app\Handlers\Form;

use Cyvian\Src\app\Classes\Form;
use Cyvian\Src\app\Classes\Locale;
use Cyvian\Src\App\Handlers\Locale\GetLocalesByType;
use Cyvian\Src\app\Repositories\LocaleRepository;

class GetValuesWithTranslationsFromForm
{
    private $localeRepository;

    public function __construct(LocaleRepository $localeRepository)
    {
        $this->localeRepository = $localeRepository;
    }

    public function handle(Form $form)
    {
        $getFieldsFromForm = new GetFieldsFromForm;
        $fields = $getFieldsFromForm->handle($form);
        $getLocalesByType = new GetLocalesByType($this->localeRepository);
        $siteLocales = $getLocalesByType->handle(Locale::IS_SITE);
        $values = [];

        foreach($fields as $field) {
            $values[$field->key] = $field->getValuesWithTranslations($siteLocales, $form->entityId);
        }

        return $values;
    }
}
