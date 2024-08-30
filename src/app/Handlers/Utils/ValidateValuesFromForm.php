<?php

namespace Cyvian\Src\app\Handlers\Utils;

use Cyvian\Src\app\Classes\Fields\Classes\FieldInterface;
use Cyvian\Src\app\Classes\Form;
use Cyvian\Src\app\Classes\Locale;
use Cyvian\Src\app\Handlers\Form\GetFieldsFromForm;
use Cyvian\Src\app\Handlers\HandlerResponse;
use Cyvian\Src\App\Handlers\Locale\GetLocalesByType;
use Cyvian\Src\App\Repositories\LocaleRepository;

class ValidateValuesFromForm
{
    public function handle(Form $form): Form
    {
        $getLocalesByType = new GetLocalesByType(new LocaleRepository);
        $validateValueFromField = new ValidateValueFromField;

        if ($form->isValidated) {
            return $form;
        }

        $locales = $getLocalesByType->handle(Locale::IS_CMS);
        foreach($form->sections as &$section) {
            foreach($section->fields as &$field) {
                $validateValueFromField->handle($field, $section->fields, $locales);
            }
        }
        $form->setHasError(ValidationStore::getInstance()->hasError);
        $form->setLocalesAffected(ValidationStore::getInstance()->localesAffected);

        return $form;
    }
}
