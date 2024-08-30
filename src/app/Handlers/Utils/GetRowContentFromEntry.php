<?php

namespace Cyvian\Src\app\Handlers\Utils;

use Cyvian\Src\app\Classes\Entry;
use Cyvian\Src\app\Handlers\Form\GetFieldsFromForm;

class GetRowContentFromEntry
{
    public function handle(Entry $entry, string $currentLocaleCode): array
    {
        $getFieldsFromForm = new GetFieldsFromForm;
        $values = [];
        $fields = $getFieldsFromForm->handle($entry->form);
        foreach ($fields as $field) {
            if ($field->displayOnList) {
                $values[$field->key] = $field->getValueForList($currentLocaleCode);
            }
        }

        return $values;
    }
}
