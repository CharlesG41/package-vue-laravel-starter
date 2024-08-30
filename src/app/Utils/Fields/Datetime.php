<?php

namespace Cyvian\Src\App\Utils\Fields;

use Cyvian\Src\App\Models\Cyvian\Entry;
use Cyvian\Src\App\Models\Cyvian\Field;
use Cyvian\Src\App\Models\Cyvian\FieldValue;
use Cyvian\Src\App\Models\Cyvian\Locale;
use Carbon\Carbon;
use Illuminate\Support\Facades\App;

class Datetime extends BaseField implements FieldInterface
{

    static public function store(Field $field, string $key, $value, Entry $entry, FieldValue $fieldValue = null): void
    {
        parent::simpleStore($field, $value, $entry, $fieldValue);
    }

    static public function value(Entry $entry, FieldValue $fieldValue)
    {
        $carbon = Carbon::createFromTimestamp($fieldValue->value);
        if (array_key_exists('format', $fieldValue->field_attributes) && $fieldValue->field_attributes['format'] != null) {
            return $carbon->format($fieldValue->_field_attributes['format']);
        } else {
            return $carbon;
        }
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
            'sanitized' => (new DateTime)->setTimestamp($value)->format(__('cyvian.general.datetime_format'))
        ];
    }

    static public function isValid($value, $field)
    {
        $message = null;
        if ($field['required'] && self::requiredHasError($value)) {
            $message = __('cyvian.errors.required');
        } elseif ($field['is_future'] && self::isPast($value)) {
            $message = __('cyvian.errors.is_future');
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

    static private function isPast($value): bool
    {
        $today = Carbon::now()->getTimestamp();
        return $value < $today;
    }
}
