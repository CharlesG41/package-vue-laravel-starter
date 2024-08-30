<?php

namespace Cyvian\Src\app\Handlers\Form;

use Cyvian\Src\app\Classes\Form;

class GetFieldFromKeyNameFromForm
{
    public function handle(Form $form, string $keyName)
    {
        $splitKeyName = explode('.', $keyName);
        $getFieldsFromForm = new GetFieldsFromForm;
        $fields = $getFieldsFromForm->handle($form);
        return $this->getFieldFromFields($fields, $splitKeyName);
    }

    private function getFieldFromFields(array $fields, array $splitKeyName)
    {
        $field = null;
        foreach($fields as $f) {
            if ($field->key == $field) {
                $field = $f;
            }
        }
        if (!$field) {
            return null;
        }
        if(count($splitKeyName) == 1) {
            return $field;
        }

        return $this->getFieldFromFields($field->fields, array_slice($splitKeyName, 1));
    }
}
