<?php

namespace Cyvian\Src\App\Utils\Fields;

use Cyvian\Src\App\Models\Cyvian\Entry;
use Cyvian\Src\App\Models\Cyvian\FieldValue;
use Cyvian\Src\App\Models\Cyvian\Field;
use Cyvian\Src\App\Utils\FieldHelper;

class Group extends BaseField implements FieldInterface
{
    static public function store(Field $field, string $key, $value, Entry $entry, FieldValue $fieldValue = null)
    {
        $fv = FieldValue::create([
            'value' => $field->key,
            'entry_id' => $entry->id,
            'field_id' => $field->id,
            'field_value_id' => $fieldValue->id ?? null
        ]);

        FieldHelper::createValues($value, $field->fields, $entry, $fv);
    }

    static public function value(Entry $entry, FieldValue $fieldValue)
    {
        $values = [];
        foreach ($fieldValue->fieldValues as $fvc) {
            $type = FieldHelper::getFieldClass($fvc->type);
            $key = $fvc->key;
            $values[$key] = $type::value($entry, $fvc);
        }

        return $values;
    }

    static public function valueWithTranslations(Entry $entry, FieldValue $fieldValue)
    {
        $values = [];
        foreach ($fieldValue->fieldValues as $fvc) {
            $type = FieldHelper::getFieldClass($fvc->type);
            $key = $fvc->key;
            $values[$key] = $type::valueWithTranslations($entry, $fvc);
        }

        return $values;
    }

    static public function isValid($value, $field)
    {
        $errors = [];
        foreach ($field['fields'] as $f) {
            $type = FieldHelper::getFieldClass($f['type']);
            $key = $f['key'];
            if (array_key_exists('translatable', $f) && $f['translatable']) {
                foreach ($value[$key] as $langCode => $v) {
                    list($fieldHasError, $errorMessage) = $type::validate($v, $f);
                    if ($fieldHasError) {
                        $errors[$key][$langCode] = $errorMessage;
                    }
                }
            } else {
                list($fieldHasError, $errorMessage) = $type::validate($value, $f);
                if ($fieldHasError) {
                    $errors[$key] = $errorMessage;
                }
            }
        }

        return [
            !empty($errors),
            $errors == [] ? null : $errors
        ];
    }
}
