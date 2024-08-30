<?php

namespace Cyvian\Src\App\Utils\Fields;

use Cyvian\Src\App\Models\Cyvian\Entry;
use Cyvian\Src\App\Models\Cyvian\Field;
use Cyvian\Src\App\Models\Cyvian\FieldValue;

class Number extends BaseField implements FieldInterface
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
        if (array_key_exists('required', $field) && $field['required'] && self::requiredHasError($value)) {
            $message = __('cyvian.errors.required');
        } elseif (array_key_exists('minimum', $field) && $field['minimum'] !== '' && self::minimumHasError($value, $field['minimum'])) {
            $message = __('cyvian.errors.number.minimum', ['min' => ($field['minimum'] - 1)]);
        } elseif (array_key_exists('maximum', $field) && $field['maximum'] !== '' && self::maximumHasError($value, $field['maximum'])) {
            $message = __('cyvian.errors.number.maximum', ['max' => ($field['maximum'] + 1)]);
        }

        return [
            $message != null,
            $message
        ];
    }

    static private function requiredHasError($value): bool
    {
        return $value == '' || $value == null;
    }

    static private function minimumHasError($value, $minimum): bool
    {
        return (int) $value < $minimum;
    }

    static private function maximumHasError($value, $maximum): bool
    {
        return (int) $value > $maximum;
    }
}
