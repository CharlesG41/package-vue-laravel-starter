<?php

namespace Cyvian\Src\App\Utils\Fields;

use Cyvian\Src\App\Models\Cyvian\Entry;
use Cyvian\Src\App\Models\Cyvian\FieldValue;
use Cyvian\Src\App\Models\Cyvian\Locale;
use Cyvian\Src\App\Models\Cyvian\Field;
use Cyvian\Src\App\Utils\EntryHelper;
use Cyvian\Src\App\Utils\FieldHelper;
use Exception;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;

class Repeater extends BaseField implements FieldInterface
{
    static public function store(Field $repeaterField, string $key, array $blocks = [], Entry $entry, FieldValue $fieldValue = null)
    {
        if ($repeaterField->translatable) {
            $locales = config('locales.locales');
            $fv1 = FieldValue::create([
                'value' => null,
                'entry_id' => $entry->id,
                'field_id' => $repeaterField->id,
                'field_value_id' => $fieldValue->id ?? null
            ]);
            foreach ($locales as $locale) {
                foreach ($blocks[$locale->code] as $block) {
                    $fv2 = FieldValue::create([
                        'value' => null,
                        'entry_id' => $entry->id,
                        'field_id' => null,
                        'locale_id' => $locale->id,
                        'field_value_id' => $fv1->id
                    ]);
                    FieldHelper::createValues($block, $repeaterField->fields, $entry, $fv2);
                }
            }
        } else {
            $fv1 = FieldValue::create([
                'value' => null,
                'entry_id' => $entry->id,
                'field_id' => $repeaterField->id,
                'field_value_id' => $fieldValue->id ?? null
            ]);
            foreach ($blocks as $block) {
                $fv2 = FieldValue::create([
                    'value' => null,
                    'entry_id' => $entry->id,
                    'field_id' => null,
                    'field_value_id' => $fv1->id
                ]);
                FieldHelper::createValues($block, $repeaterField->fields, $entry, $fv2);
            }
        }
    }

    // static public function store1(Field $repeaterField, string $key, array $blocks = [], Entry $entry, FieldValue $fieldValue = null)
    // {
    //     $locales = config('locales.locales');
    //     if (array_key_exists('translatable', $repeaterField->field_attributes) && $repeaterField->field_attributes['translatable']) {
    //         foreach ($locales as $locale) {
    //             foreach ($blocks[$locale->code] as $block) {
    //                 $fv = FieldValue::create([
    //                     'value' => null,
    //                     'entry_id' => $entry->id,
    //                     'field_id' => $repeaterField->id,
    //                     'locale_id' => $locale->id,
    //                     'field_value_id' => $fieldValue->id ?? null
    //                 ]);
    //                 FieldHelper::createValues($block, $repeaterField->fields, $entry, $fv);
    //             }
    //         }
    //     } else {
    //         foreach ($blocks as $block) {
    //             $fv = FieldValue::create([
    //                 'value' => null,
    //                 'entry_id' => $entry->id,
    //                 'field_id' => $repeaterField->id,
    //                 'field_value_id' => $fieldValue->id ?? null
    //             ]);
    //             FieldHelper::createValues($block, $repeaterField->fields, $entry, $fv);
    //         }
    //     }
    // }

    static public function value(Entry $entry, FieldValue $fieldValue)
    {
        $values = [];
        if ($fieldValue->translatable) {
            $locale = config('locales.current_locale');
            $fieldValueBlocks = $fieldValue->fieldValues()->where('locale_id', $locale->id)->where('entry_id', $entry->id)->get();
        } else {
            $fieldValueBlocks = $fieldValue->fieldValues()->where('entry_id', $entry->id)->get();
        }
        foreach ($fieldValueBlocks as $fieldValueBlock) {
            $block = [];
            foreach ($fieldValueBlock->fieldValues as $fieldValueField) {
                $key = $fieldValueField->key;
                $type = FieldHelper::getFieldClass($fieldValueField->type);
                $block[$key] = $type::value($entry, $fieldValueField);
            }
            $values[] = $block;
        }
        return $values;
    }

    static public function valueWithTranslations(Entry $entry, FieldValue $fieldValue)
    {
        $values = [];
        if ($fieldValue->translatable) {
            $locales = config('locales.locales');
            foreach ($locales as $locale) {
                $blocks = [];
                foreach ($fieldValue->fieldValues->where('locale_id', $locale->id) as $fieldValueChild) {
                    $block = [];
                    foreach ($fieldValueChild->fieldValues as $fieldValueData) {
                        $key = $fieldValueData->key;
                        $type = FieldHelper::getFieldClass($fieldValueData->type);
                        $block[$key] = $type::valueWithTranslations($entry, $fieldValueData);
                    }
                    $blocks[] = $block;
                }
                $values[$locale->code] = $blocks;
            }
        } else {
            foreach ($fieldValue->fieldValues as $fieldValueChild) {
                $block = [];
                foreach ($fieldValueChild->fieldValues as $fieldValueData) {
                    $key = $fieldValueData->key;
                    $type = FieldHelper::getFieldClass($fieldValueData->type);
                    $block[$key] = $type::valueWithTranslations($entry, $fieldValueData);
                }
                $values[] = $block;
            }
        }
        return $values;
    }

    static public function isValid($values, $field)
    {
        $errors = [];
        foreach ($values as $i => $value) {
            if(is_string($value)) {
                throw new Exception('400', "Respeater.php");
            }
            $arrayErrors = EntryHelper::validateValues($value, $field['fields']);
            if (!empty($arrayErrors)) {
                $errors[$i] = EntryHelper::validateValues($value, $field['fields']);
            }
        }

        return [
            !empty($errors),
            $errors == [] ? null : $errors
        ];
    }
}
