<?php

namespace Cyvian\Src\app\Handlers\Form;

use Cyvian\Src\app\Classes\Form;
use Cyvian\Src\app\Handlers\HandlerResponse;

class ValidateEmptyKeysFromForm
{
    public function handle(Form $form): array
    {
        $getFieldsFromForm = new GetFieldsFromForm;
        $fields = $getFieldsFromForm->handle($form);
        $errors = [];
        foreach($fields as $field) {
            $errors = array_merge($errors, $this->validateEmptyKeyFromField($field));
        }
        return $errors;
    }

    public function validateEmptyKeyFromField($field): array
    {
        $errors = [];
        $fieldErrors = [];
        if($field->key === '') {
            $errors[] =  $field->key;
        }
        if(class_uses($field)) {
            $fieldErrors = $this->validateEmptyKeyFromField($field);
        }

        if (count($fieldErrors) > 0) {
            $errors = array_merge($errors, $fieldErrors);
        }

        return $errors;
    }
}
