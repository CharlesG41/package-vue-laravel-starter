<?php

namespace Cyvian\Src\App\Utils\Fields;

use Cyvian\Src\App\Models\Cyvian\Entry;
use Cyvian\Src\App\Models\Cyvian\FieldValue;
use Cyvian\Src\App\Models\Cyvian\Locale;
use Cyvian\Src\App\Models\Cyvian\Field;
use Cyvian\Src\App\Utils\FieldHelper;
use Illuminate\Support\Facades\App;

class Flexible extends BaseField implements FieldInterface
{
    static public function store(Field $flexibleField, string $key, $sections, Entry $entry, FieldValue $fieldValue = null)
    {
        $locales = config('locales.locales');
        $fieldValue = FieldValue::create([
            'value' => $flexibleField->key,
            'entry_id' => $entry->id,
            'field_id' => $flexibleField->id,
            'field_value_id' => $fieldValue->id ?? null
        ]);
        if($flexibleField->translatable) {
            foreach($locales as $locale) {
                foreach($sections[$locale->code] as $section) {
                    $f = null;
                    foreach ($flexibleField->fields as $field) {
                        if ($section['key'] == $field->key) {
                            $f = $field;
                            break;
                        }
                    }
                    $fv = FieldValue::create([
                        'value' => $section['key'],
                        'entry_id' => $entry->id,
                        'field_id' => $f->id,
                        'locale_id' => $locale->id,
                        'field_value_id' => $fieldValue->id ?? null
                    ]);
                    FieldHelper::createValues($section['value'], $f->fields, $entry, $fv);
                }
            }
        } else {
            foreach($sections as $section) {
                $f = null;
                foreach ($flexibleField->fields as $field) {
                    if ($section['key'] == $field->key) {
                        $f = $field;
                        break;
                    }
                }
                $fv = FieldValue::create([
                    'value' => $section['key'],
                    'entry_id' => $entry->id,
                    'field_id' => $f->id,
                    'locale_id' => null,
                    'field_value_id' => $fieldValue->id ?? null
                ]);
                FieldHelper::createValues($section['value'], $f->fields, $entry, $fv);
            }
        }
        // foreach($locales as $locale)
        // if($flexibleField->translatable) {
        //     foreach($locales as $locale) {
        //         foreach ($sections as $key => $section) {
        //             $f = null;
        //             foreach ($flexibleField->fields as $field) {
        //                 if ($section['key'] == $field->key) {
        //                     $f = $field;
        //                     break;
        //                 }
        //             }
        //             if ($f->translatable) {
        //                 foreach ($locales as $locale) {
        //                     $fv = FieldValue::create([
        //                         'value' => $section['key'],
        //                         'entry_id' => $entry->id,
        //                         'field_id' => $f->id,
        //                         'locale_id' => $locale->id,
        //                         'field_value_id' => $fieldValue->id ?? null
        //                     ]);
        //                     FieldHelper::createValues($section['value'], $f->fields, $entry, $fv);
        //                 }
        //             } else {
        //                 $fv = FieldValue::create([
        //                     'value' => $section['key'],
        //                     'entry_id' => $entry->id,
        //                     'field_id' => $f->id,
        //                     'field_value_id' => $fieldValue->id ?? null
        //                 ]);
        //                 FieldHelper::createValues($section['value'], $f->fields, $entry, $fv);
        //             }
        //         }
        //     }
        // }
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
                'value' => []
            ];
            foreach ($fieldValueSection->fieldValues as $fieldValueSectionChild) {
                $key = $fieldValueSectionChild->key;
                $type = FieldHelper::getFieldClass($fieldValueSectionChild->type);
                if ($fieldValueSectionChild->translatable) {
                    if ($fieldValueSectionChild->locale_id === $locale->id || $fieldValueSectionChild->type === 'repeater') {
                        $block['value'][$key] = $type::value($entry, $fieldValueSectionChild);
                    }
                } else {
                    $block['value'][$key] = $type::value($entry, $fieldValueSectionChild);
                }
            }
            $values[] = $block;
        }

        return $values;
    }

    static public function valueWithTranslations(Entry $entry, FieldValue $fieldValue)
    {
        $values = [];
        if($fieldValue->translatable) {
            $locales = config('locales.locales');
            foreach($locales as $locale) {
                $values[$locale->code] = [];
                foreach ($fieldValue->fieldValues->where('locale_id', $locale->id) as $fieldValueSection) {
                    $block = [
                        'key' => $fieldValueSection->key,
                        'value' => []
                    ];
                    foreach ($fieldValueSection->fieldValues as $fieldValueSectionChild) {
                        $key = $fieldValueSectionChild->key;
                        $type = FieldHelper::getFieldClass($fieldValueSectionChild->type);
                        $block['value'][$key] = $type::valueWithTranslations($entry, $fieldValueSectionChild);
                    }
                    $values[$locale->code][] = $block;
                }
            }
        } else {
            foreach ($fieldValue->fieldValues as $fieldValueSection) {
                $block = [
                    'key' => $fieldValueSection->key,
                    'value' => []
                ];
                foreach ($fieldValueSection->fieldValues as $fieldValueSectionChild) {
                    $key = $fieldValueSectionChild->key;
                    $type = FieldHelper::getFieldClass($fieldValueSectionChild->type);
                    $block['value'][$key] = $type::valueWithTranslations($entry, $fieldValueSectionChild);
                }
                $values[] = $block;
            }
        }
        return $values;
    }

    static public function isValid($values, $field)
    {
        // $errors = [];
        // foreach ($values as $i => $value) {
        //     $arrayErrors = EntryHelper::validateValues($value, $field['fields']);
        //     if (!empty($arrayErrors)) {
        //         $errors[$i] = EntryHelper::validateValues($value, $field['fields']);
        //     }
        // }

        // return [
        //     !empty($errors),
        //     $errors == [] ? null : $errors
        // ];
        return [
            false,
            null
        ];
    }
}
