<?php

namespace Cyvian\Src\app\Handlers\Form;

use Cyvian\Src\app\Classes\Form;

class GetDefaultFieldValueFromForm
{
    public function handle(Form $form, array $siteLocales, string $currentLocaleCode, int $entryId)
    {
        $getFieldsFromForm = new GetFieldsFromForm;
        $fields = $getFieldsFromForm->handle($form);
        $field = $fields[0];
        $field->setValueFromDatabase($siteLocales, $entryId);

        return $field->getTranslatedValue($currentLocaleCode);
    }
}
