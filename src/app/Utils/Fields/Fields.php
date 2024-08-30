<?php

namespace Cyvian\Src\App\Utils\Fields;

use Cyvian\Src\App\Models\Cyvian\Entry;
use Cyvian\Src\App\Models\Cyvian\FieldValue;
use Cyvian\Src\App\Models\Cyvian\Locale;
use Cyvian\Src\App\Models\Cyvian\Field;
use Cyvian\Src\App\Utils\FieldHelper;
use Illuminate\Support\Facades\App;

class Fields extends BaseField implements FieldInterface
{
    static public function store(Field $field, string $key, $values, Entry $entry, FieldValue $fieldValue = null)
    {
        $locales = config('locales.locales');
        $fieldValue = FieldValue::create([
            'value' => $field->key,
            'entry_id' => $entry->id,
            'field_id' => $field->id,
            'field_value_id' => $fieldValue->id ?? null
        ]);
        // foreach ($sections as $key => $section) {
        //     $f = null;
        //     foreach ($flexibleField->fields as $field) {
        //         if ($section['key'] == $field->key) {
        //             $f = $field;
        //             break;
        //         }
        //     }
        //     if ($f->translatable) {
        //         foreach ($locales as $locale) {
        //             $fv = FieldValue::create([
        //                 'value' => $section['key'],
        //                 'entry_id' => $entry->id,
        //                 'field_id' => $f->id,
        //                 'locale_id' => $locale->id,
        //                 'field_value_id' => $fieldValue->id ?? null
        //             ]);
        //             FieldHelper::createValues($section['value'], $f->fields, $entry, $fv);
        //         }
        //     } else {
        //         $fv = FieldValue::create([
        //             'value' => $section['key'],
        //             'entry_id' => $entry->id,
        //             'field_id' => $f->id,
        //             'field_value_id' => $fieldValue->id ?? null
        //         ]);
        //         FieldHelper::createValues($section['value'], $f->fields, $entry, $fv);
        //     }
        // }
    }

    static public function value(Entry $entry, FieldValue $fieldValue)
    {
        $values = [];
        $locale = config('locales.current_locale');
        foreach ($fieldValue->fieldValues as $fieldValueSection) {
            $block = [
                'key' => $fieldValueSection->key,
                'values' => []
            ];
            foreach ($fieldValueSection->fieldValues as $fieldValueSectionChild) {
                $key = $fieldValueSectionChild->key;
                $type = FieldHelper::getFieldClass($fieldValueSectionChild->type);
                if ($fieldValueSectionChild->translatable) {
                    if ($fieldValueSectionChild->locale_id === $locale->id || $fieldValueSectionChild->type === 'repeater') {
                        $block['values'][$key] = $type::value($entry, $fieldValueSectionChild);
                    }
                } else {
                    $block['values'][$key] = $type::value($entry, $fieldValueSectionChild);
                }
            }
            $values[] = $block;
        }

        return $values;
    }

    static public function valueWithTranslations(Entry $entry, FieldValue $fieldValue)
    {
        $values = [];
        foreach ($fieldValue->fieldValues as $fieldValueSection) {
            $block = [
                'key' => $fieldValueSection->key,
                'values' => []
            ];
            foreach ($fieldValueSection->fieldValues as $fieldValueSectionChild) {
                $key = $fieldValueSectionChild->key;
                $type = FieldHelper::getFieldClass($fieldValueSectionChild->type);
                $block['values'][$key] = $type::valueWithTranslations($entry, $fieldValueSectionChild);
            }
            $values[] = $block;
        }

        return $values;
    }

    static public function isValid($value, $field)
    {
        // $message = null;
        // if (array_key_exists('translatable', $field) && $field['translatable']) {
        //     foreach ($value as $v) {
        //         if (array_key_exists('required', $field) && $field['required']) {
        //             if (self::requiredHasError($v)) {
        //                 $message = __('cyvian.errors.required');
        //             }
        //         }
        //     }
        // } else {
        //     if (array_key_exists('required', $field) && $field['required']) {
        //         if (self::requiredHasError($value)) {
        //             $message = __('cyvian.errors.required');
        //         }
        //     }
        // }

        // return [
        //     $message != null,
        //     $message
        // ];

        return [
            false,
            null
        ];
    }
}
