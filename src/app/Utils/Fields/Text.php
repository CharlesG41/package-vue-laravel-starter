<?php

namespace Cyvian\Src\App\Utils\Fields;

use Cyvian\Src\App\Models\Cyvian\Entry;
use Cyvian\Src\App\Models\Cyvian\Field;
use Cyvian\Src\App\Models\Cyvian\FieldValue;

class Text extends BaseField implements FieldInterface
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
        } elseif (array_key_exists('regex', $field) && self::regexHasError($value, $field['regex'])) {
            $message = $field['regex_message'] ?? __('cyvian.errors.regex', ['regex' => $field['regex']]);
        } elseif (array_key_exists('minimum', $field) && self::minimumHasError($value, $field['minimum'])) {
            $message = __('cyvian.errors.minimum', ['min' => $field['minimum']]);
        } elseif (array_key_exists('maximum', $field) && self::maximumHasError($value, $field['maximum'])) {
            $message = __('cyvian.errors.maximum', ['max' => $field['maximum']]);
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

    static private function regexHasError($value, $regex): bool
    {
        if ($regex === '' || $regex === null) {
            return false;
        } else {
            return preg_match($regex, $value);
        }
    }

    static private function minimumHasError($value, $minimum): bool
    {
        if ($minimum === '' || $minimum === null) {
            return false;
        } else {
            return strlen($value) < $minimum;
        }
    }

    static private function maximumHasError($value, $maximum): bool
    {
        if ($maximum === '' || $maximum === null) {
            return false;
        } else {
            return strlen($value) > $maximum;
        }
    }
}
