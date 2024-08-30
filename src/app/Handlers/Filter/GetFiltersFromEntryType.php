<?php

namespace Cyvian\Src\app\Handlers\Filter;

use Cyvian\Src\app\Classes\EntryType;
use Cyvian\Src\app\Classes\Locale;
use Cyvian\Src\app\Handlers\Form\GetFieldsFromForm;
use Cyvian\Src\app\Handlers\Utils\SnakeToCamel;

class GetFiltersFromEntryType
{
    public function handle(EntryType $entryType, Locale $currentLocale)
    {
        $getFieldsFromForm = new GetFieldsFromForm;
        $instantiateFilterFromField = new InstantiateFilterFromField;

        $fields = $getFieldsFromForm->handle($entryType->form);
        $filters = [];

        foreach ($fields as $field) {
            if ($field->hasFilter) {
                $filters[] = $instantiateFilterFromField->handle($field, $currentLocale);
            }
        }

        return $filters;
    }
}
