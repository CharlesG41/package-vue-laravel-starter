<?php

namespace Cyvian\Src\App\Utils\Fields;

use Cyvian\Src\App\Models\Cyvian\Entry;
use Cyvian\Src\App\Models\Cyvian\Field;
use Cyvian\Src\App\Models\Cyvian\FieldValue;

class Url extends BaseField implements FieldInterface
{
    static public function store(Field $field, string $key, $value, Entry $entry, FieldValue $fieldValue = null): void
    {
        parent::simpleStore($field, $value, $entry, $fieldValue);
    }

    static public function value(Entry $entry, FieldValue $fieldValue)
    {
        return $fieldValue->value;
    }

    static public function valueWithTranslations(Entry $entry, FieldValue $fieldValue)
    {
        $values = FieldValue::where('entry_id', $entry->id)->where('field_id', $fieldValue->field_id)->get();
        if ($fieldValue->translatable) {
            return $values->mapWithKeys(function ($fieldValue) {
                return [$fieldValue->locale->code => $fieldValue->value];
            });
        }

        return $fieldValue->value;
    }

    static public function valueForList($value, int $fieldId)
    {
        return [
            'original' => $value,
            'sanitized' => $value
        ];
    }

    static public function isValid($value, $field)
    {
        $message = null;
        if ($field['required'] && self::requiredHasError($value)) {
            $message = __('cyvian.errors.required');
        } elseif (filter_var($value, FILTER_VALIDATE_URL) === false) {
            $message = __('cyvian.errors.url');
        }

        return [
            $message !== null,
            $message
        ];
    }

    static private function requiredHasError($value): bool
    {
        return $value == '' || $value == null;
    }
}