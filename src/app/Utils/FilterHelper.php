<?php

namespace Cyvian\Src\App\Utils;

use Cyvian\Src\app\Classes\EntryType;
use Cyvian\Src\app\Classes\Locale;
use Cyvian\Src\App\Models\Cyvian\Field;
use Cyvian\Src\App\Models\Cyvian\Filter;

class FilterHelper
{
    static public function getFilters(EntryType $entryType, Locale $currentLocale): array
    {
//        $filters = [];
//        return $filters;
        foreach ($entryType->fields as $field) {
            if ($field->filter) {
                $function = Helper::snaketoCamel($field->type);
                $types = [
                    'boolean' => 'Boolean',
                    'oneEntry' => 'SelectOne',
                    'manyEntries' => 'SelectMany',
                    'selectOne' => 'SelectOne',
                    'selectMany' => 'SelectMany'
                ];
                $filters[] = array_merge([
                    'label' => $field->name->{$currentLocale->code},
                    'type' => $types[$function],
                    'field_to_search' => $field->key,
                ], self::$function($field, $entryType));
            }
        }

        return $filters;
    }

    static private function oneEntry(Field $field, EntryType $entryType): array
    {
        $options = [];
        foreach ($field->field_attributes['entry_types'] as $entryTypeId) {
            $et = EntryType::find($entryTypeId);
            if ($et !== null) {
                foreach ($et->entries as $entry) {
                    $options[$entry->id] = $entry->mainValue;
                }
            }
        }

        return ['options' => $options];
    }

    static private function manyEntries(Field $field, EntryType $entryType): array
    {
        return self::oneEntry($field, $entryType);
    }

    static private function boolean(Field $field, EntryType $entryType): array
    {
        return [];
    }

    static private function selectOne(Field $field, EntryType $entryType): array
    {
        $locale = config('locales.current_locale');
        $options = $field->field_attributes['options'];
        $optionsArray = [];
        foreach ($options as $key => $value) {
            $optionsArray[$key] = $value->{$locale->code};
        }
        return ['options' => $optionsArray];
    }

    static private function selectMany(Field $field, EntryType $entryType): array
    {
        return self::selectOne($field, $entryType);
    }

    static public function createFilters(array $filters, array $fields, EntryType $entryType): void
    {

        foreach ($filters as $filter) {
            foreach ($fields as $field) {
                if ($field->field_attributes['key'] === $filter) {
                    Filter::create([
                        'field_id' => $field->id,
                        'entry_type_id' => $entryType->id,
                    ]);
                    continue 2;
                }
            }
        }
    }
}
