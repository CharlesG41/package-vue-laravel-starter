<?php

namespace Cyvian\Src\App\Utils\Fields;

use Cyvian\Src\App\Models\Cyvian\Entry;
use Cyvian\Src\App\Models\Cyvian\Field;
use Cyvian\Src\App\Models\Cyvian\FieldValue;
use Cyvian\Src\App\Models\Cyvian\Locale;
use Illuminate\Support\Facades\App;

class OneEntry extends BaseField implements FieldInterface
{
    static public function store(Field $field, string $key, $value, Entry $entry, FieldValue $fieldValue = null): void
    {
        if ($field->translatable) {
            $locales = config('locales.locales');
            foreach ($locales as $locale) {
                FieldValue::create([
                    'value' => $value[$locale->code],
                    'field_id' => $field->id,
                    'field_value_id' => $fieldValue->id ?? null,
                    'entry_id' => $entry->id,
                    'locale_id' => $locale->id,
                ]);
            }
        } else {
            FieldValue::create([
                'value' => $value,
                'field_id' => $field->id,
                'field_value_id' => $fieldValue->id ?? null,
                'entry_id' => $entry->id,
            ]);
        }
    }

    static public function value(Entry $entry, FieldValue $fieldValue)
    {
        $entry = null;
        $value = [];
        if ($fieldValue->field_attributes['id']) {
            $value['id'] = $fieldValue->value;
        }
        $entry = null;
        if ($fieldValue->field_attributes['main_value']) {
            $entry = Entry::find($fieldValue->value);
            $value['main_value'] = $entry->mainValue;
        }
        if ($fieldValue->field_attributes['entry']) {
            if (!$entry) {
                $entry = Entry::find($fieldValue->value);
            }
            $value['entry'] = $entry;
        }

        return $value;
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

    static public function valueForList($value, int $fieldId, int $entryId)
    {
        $original = $value;
        $sanitized = Entry::find((int) $value)->mainValue ?? $value;
        return [
            'original' => (int) $original,
            'sanitized' => $sanitized
        ];
    }

    static public function isValid($value, $field)
    {
        $message = null;
        if ($field['required'] && self::requiredHasError($value)) {
            $message = __('cyvian.errors.required');
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
}
