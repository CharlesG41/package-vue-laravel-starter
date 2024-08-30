<?php

namespace Cyvian\Src\App\Utils;

use Cyvian\Src\App\Models\Cyvian\Entry;
use Cyvian\Src\App\Models\Cyvian\EntryType;
use Illuminate\Support\Facades\Auth;

class EntryHelper
{
    static public function createEntry(EntryType $entryType): Entry
    {
        $order = $entryType->entries()->max('order') + 1;
        return Entry::create([
            'order' => $order,
            'created_by' => Auth::id() ?? 1,
            'updated_by' => Auth::id() ?? 1,
            'entry_type_id' => $entryType->id,
        ]);
    }

    static public function validateValues(array $values, array $fields): array
    {
        $errors = [];
        $locales = config('locales.locales');
        foreach ($fields as $field) {
            $type = FieldHelper::getFieldClass($field['type']);
            $value = null;
            foreach ($values as $k => $v) {
                if ($k == $field['key']) {
                    $value = $v;
                    break;
                }
            }
            if (array_key_exists('translatable', $field) && $field['translatable']) {
                if (!is_array($value) || $value === null) {
                    foreach ($locales as $locale) {
                        $value[$locale->code] = null;
                    }
                }
                foreach ($value as $langCode => $v) {
                    if (self::needsToBeValidated($field, $fields, $values)) {
                        list($fieldHasError, $errorMessage) = $type::validate($v, $field);
                        if ($fieldHasError) {
                            $errors[$field['key']][$langCode] = $errorMessage;
                        }
                    }
                }
            } else {
                if (self::needsToBeValidated($field, $fields, $values)) {
                    list($fieldHasError, $errorMessage) = $type::validate($value, $field);
                    if ($fieldHasError) {
                        $errors[$field['key']] = $errorMessage;
                    }
                }
            }
        }

        return $errors;
    }

    static private function needsToBeValidated(array $fieldWithCondition, array $fields, array $form, ?string $langCode = null): bool
    {
        // if no condiitons, the field needs to be validated.
        if (!array_key_exists('conditions', $fieldWithCondition)) {
            return true;
        }
        $hasError = false;
        // if it has one or more, we parse thtough them to see if the condition is met
        foreach ($fieldWithCondition['conditions'] as $condition) {
            $parentField = array_values(array_filter($fields, function ($f) use ($condition) {
                return $condition['field'] === $f['key'];
            }))[0];
            // in the case the parent field (field to contains the value to be checked on) has a condition itself, we check if the field needs to be validated too. See actions like create or edit for example
            if (!self::needsToBeValidated($parentField, $fields, $form, $langCode)) {
                return false;
            }
            $parentValue = $form[$parentField['key']];

            if ($parentField['translatable']) {
                $parentValue = $parentValue[$langCode];
            }
            // check if condition is met
            switch ($condition['operator']) {
                case "=":
                    if ($parentValue != $condition['value']) {
                        $hasError = true;
                    }
                case ">":
                    if ($parentValue > $condition['value']) {
                        $hasError = true;
                    }
                case "<":
                    if ($parentValue < $condition['value']) {
                        $hasError = true;
                    }
            }
        }
        return !$hasError;
    }
}
