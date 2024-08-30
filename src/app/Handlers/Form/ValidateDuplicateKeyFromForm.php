<?php

namespace Cyvian\Src\app\Handlers\Form;

use Cyvian\Src\app\Classes\Form;

class ValidateDuplicateKeyFromForm
{
    public function handle(Form $form): array
    {
        return [];
        $keys = [];
        $errors = [];

        /// loop through sections
        // if section key is not null, then check if key is in keys array
        // if yes put in errors array as $key => $message

        foreach ($form->sections as $section) {
            $fieldErrors = [];
            foreach ($section->fields as $field) {
                if (property_exists($field, 'fields')) {
                    $fieldError = $this->checkFormDuplicateInFields($field->fields);
                    if (!empty($fieldError)) {
                        $fieldErrors[$field->key] = $fieldError;
                    }
                }
                if ($section->key === null) {
                    $keys[] = $field->key;
                    if (in_array($field->key, $keys)) {
                        $errors[$field->key] = __('cyvian.errors.same_key');
                    }
                } else {
                    if (!array_key_exists($section->key, $keys)) {
                        $keys[$section->key] = [];
                    }
                    $keys[$section->key][] = $field->key;
                    if (in_array($field->key, $keys[$section->key])) {
                        if (!array_key_exists($section->key, $errors)) {
                            $errors[$section->key] = [];
                        }
                        $errors[$section->key][$field->key] = __('cyvian.errors.same_key');
                    }
                }
            }
            if ($section->key !== null) {
                $keys[] = $section->key;
            }
        }

        return $errors;
    }

    private function checkFormDuplicateInFields(array $fields): array
    {
        $fieldKeys = [];
        foreach ($fields as $field) {
            if (property_exists($field, 'fields')) {
                $fieldKeys = array_merge($fieldKeys, $this->checkFormDuplicateInFields($field->fields));
            }
            if (in_array($field->key, $fieldKeys)) {
                $fieldKeys[$field->key] = __('cyvian.errors.same_key');
            }
        }

        return $fieldKeys;
    }
}
