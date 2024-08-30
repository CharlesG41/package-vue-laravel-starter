<?php

namespace Cyvian\Src\App\Utils\Fields;

use Cyvian\Src\App\Models\Cyvian\Entry;
use Cyvian\Src\App\Models\Cyvian\Field;
use Cyvian\Src\App\Models\Cyvian\FieldValue;

class ManyEntries extends BaseField implements FieldInterface
{
    static public function store(Field $field, string $key, $values, Entry $entry, FieldValue $fieldValue = null): void
    {
        $fv1 = FieldValue::create([
            'value' => null,
            'entry_id' => $entry->id,
            'field_id' => $field->id,
            'field_value_id' => $fieldValue->id ?? null
        ]);
        if ($field->translatable) {
            $locales = config('locales.locales');
            foreach ($locales as $locale) {
                foreach ($values[$locale->code] as $value) {
                    FieldValue::create([
                        'value' => $value,
                        'entry_id' => $entry->id,
                        'locale_id' => $locale->id,
                        'field_value_id' => $fv1->id
                    ]);
                }
            }
        } else {
            foreach ($values as $value) {
                FieldValue::create([
                    'value' => $value,
                    'entry_id' => $entry->id,
                    'field_value_id' => $fv1->id
                ]);
            }
        }
    }

    static public function value(Entry $entry, FieldValue $fieldValue)
    {
        if ($fieldValue->translatable) {
            $locale = config('locales.current_locale');
            $fieldValues = $fieldValue->fieldValues()->where('locale_id', $locale->id)->get();
        } else {
            $fieldValues = $fieldValue->fieldValues;
        }
        $values = [];
        foreach ($fieldValues as $fv) {
            $values[] = Entry::find($fv->value)->values;
        }
        return $values;
    }

    static public function valueWithTranslations(Entry $entry, FieldValue $fieldValue)
    {
        $values = [];
        if ($fieldValue->translatable) {
            $locales = config('locales.locales');
            foreach ($locales as $locale) {
                $values[$locale->code] = [];
                foreach ($fieldValue->fieldValues->where('locale_id', $locale->id) as $fieldValueChild) {
                    $values[$locale->code][] = $fieldValueChild->value;
                }
            }
        } else {
            foreach ($fieldValue->fieldValues as $fieldValueChild) {
                $values[] = $fieldValueChild->value;
            }
        }
        return $values;
    }

    static public function valueForList($value, int $fieldId, $entryId)
    {
        $originalArray = [];
        $sanitizedArray = [];
        $field = Field::find($fieldId);
        if ($field->translatable) {
            $fieldValue = FieldValue::where('field_id', $field->id)->where('entry_id', $entryId)->get()->first();
            $locale = config('locales.current_locale');
            foreach ($fieldValue->fieldValues()->where('locale_id', $locale->id)->get() as $fieldValueWithId) {
                $originalArray[] = (int)$fieldValueWithId->value;
                $sanitizedArray[] = Entry::find((int) $fieldValueWithId->value)->mainValue ?? $fieldValueWithId->value;
            }
        } else {
            $fieldValue = FieldValue::where('field_id', $field->id)->where('entry_id', $entryId)->get()->first();
            foreach ($fieldValue->fieldValues as $fieldValueWithId) {
                $originalArray[] = (int)$fieldValueWithId->value;
                $sanitizedArray[] = Entry::find((int) $fieldValueWithId->value)->mainValue ?? $fieldValueWithId->value;
            }
        }
        return [
            'original' => $originalArray,
            'sanitized' => implode(', ', $sanitizedArray)
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
