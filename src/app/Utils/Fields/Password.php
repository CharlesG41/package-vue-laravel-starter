<?php

namespace Cyvian\Src\App\Utils\Fields;

use Cyvian\Src\App\Models\Cyvian\Entry;
use Cyvian\Src\App\Models\Cyvian\Field;
use Cyvian\Src\App\Models\Cyvian\FieldValue;
use Illuminate\Support\Facades\Hash;

class Password extends BaseField implements FieldInterface
{
    static public function store(Field $field, string $key, $value, Entry $entry, FieldValue $fieldValue = null): void
    {
        parent::simpleStore($field, Hash::make($value), $entry, $fieldValue);
    }

    static public function value(Entry $entry, FieldValue $fieldValue)
    {
        return $fieldValue->value;
    }

    static public function valueWithTranslations(Entry $entry, FieldValue $fieldValue)
    {
        $values = FieldValue::where('entry_id', $entry->id)->where('field_id', $fieldValue->field_id)->get();
        if ($fieldValue->field->translatable) {
            return $values->mapWithKeys(function ($fieldValue) {
                return [$fieldValue->locale->code => null];
            });
        }

        return null;
    }

    static public function isValid($value, $field)
    {
        $message = null;
        if (array_key_exists('regex', $field) && $field['regex'] !== '' && self::regexHasError($value, $field['regex'])) {
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
        if (!$value || $minimum === '' || $minimum === null) {
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
