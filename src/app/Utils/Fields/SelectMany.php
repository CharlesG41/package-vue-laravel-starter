<?php

namespace Cyvian\Src\App\Utils\Fields;

use Cyvian\Src\App\Models\Cyvian\Entry;
use Cyvian\Src\App\Models\Cyvian\Field;
use Cyvian\Src\App\Models\Cyvian\FieldValue;

class SelectMany extends BaseField implements FieldInterface
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
        $values = [];
        $locale = config('locales.current_locale');
        if ($fieldValue->translatable) {
            $fieldValues = $fieldValue->fieldValues()->where('locale_id', $locale->id)->get();
        } else {
            $fieldValues = $fieldValue->fieldValues;
        }
        $options = $fieldValue->field_attributes['options'];
        foreach ($fieldValues as $fv) {
            if ($fieldValue->field_attributes['get_keys']) {
                $values[] = $fv->value;
            } else {
                $values[] = $options->{$fv->value}->{$locale->code};
            }
        }

        return $values;
    }

    static public function valueWithTranslations(Entry $entry, FieldValue $fieldValue)
    {
        // $values = FieldValue::where('entry_id', $entry->id)->where('field_id', $fieldValue->field_id)->get();
        // if ($fieldValue->translatable) {
        //     return $fieldValue->fieldValues->mapWithKeys(function ($fieldValue) {
        //         return [$fieldValue->locale->code => $fieldValue->value];
        //     });
        // }
        // return $fieldValue->value;
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

    static public function valueForList($value, int $fieldId, int $entryId)
    {
        $originalArray = [];
        $sanitizedArray = [];
        $field = Field::find($fieldId);
        $options = $field->field_attributes['options'];
        $locale = config('locales.current_locale');
        if ($field->translatable) {
            $fieldValue = FieldValue::where('field_id', $field->id)->where('entry_id', $entryId)->get()->first();
            foreach ($fieldValue->fieldValues()->where('locale_id', $locale->id) as $fieldValueWithId) {
                $originalArray[] = $fieldValueWithId->value;
                $sanitizedArray[] = $options->{$fieldValueWithId->value}->{$locale->code};
            }
        } else {
            $fieldValue = FieldValue::where('field_id', $field->id)->where('entry_id', $entryId)->get()->first();
            foreach ($fieldValue->fieldValues as $fieldValueWithId) {
                $originalArray[] = $fieldValueWithId->value;
                $sanitizedArray[] = $options->{$fieldValueWithId->value}->{$locale->code};
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
