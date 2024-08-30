<?php

namespace Cyvian\Src\App\Utils\Fields;

use Cyvian\Src\App\Models\Cyvian\Entry;
use Cyvian\Src\App\Models\Cyvian\Field;
use Cyvian\Src\App\Models\Cyvian\FieldValue;
use Carbon\Carbon;

class Date extends BaseField implements FieldInterface
{
    static public function store(Field $field, string $key, $value, Entry $entry, FieldValue $fieldValue = null): void
    {
        parent::simpleStore($field, $value, $entry, $fieldValue);
    }

    static public function value(Entry $entry, FieldValue $fieldValue)
    {
        $datetime = (new DateTime)->setTimestamp($fieldValue->value);
        if ($fieldValue->field_attributes['format']) {
            return $datetime->format($fieldValue->_field_attributes['format']);
        } else {
            return $datetime;
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
            'sanitized' => (new DateTime)->setTimestamp($value)->format(__('cyvian.general.date_format'))
        ];
    }

    static public function isValid($value, $field)
    {
        $message = null;
        if ($field['required'] && self::requiredHasError($value)) {
            $message = __('cyvian.errors.required');
        } elseif ($field['is_future'] && self::isPast($value, $field['include_today'])) {
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

    static private function isPast($value, $includeToday): bool
    {
        $today = Carbon::today()->getTimestamp();
        if (
            $value < $today ||
            !$includeToday && $value == $today
        ) {
            return true;
        }

        return false;
    }
}
