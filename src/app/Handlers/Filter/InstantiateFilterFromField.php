<?php

namespace Cyvian\Src\app\Handlers\Filter;

use Cyvian\Src\app\Classes\Filters\BooleanFilter;
use Cyvian\Src\app\Classes\Filters\ManyEntriesFilter;
use Cyvian\Src\app\Classes\Filters\SelectManyFilter;
use Cyvian\Src\app\Classes\Filters\SelectOneFilter;
use Cyvian\Src\app\Classes\Locale;

class InstantiateFilterFromField
{
    public function handle($field, Locale $currentLocale)
    {
        if ($field->type === 'boolean') {
            return new BooleanFilter($field->id, $field->name->getCurrent($currentLocale));
        }

        if ($field->type === 'select_one') {
            $options = [];
            foreach ($field->options as $key => $labels) {
                $options[$key] = $labels[$currentLocale->code];
            }
            return new SelectOneFilter($field->id, $field->name->getCurrent($currentLocale), $options);
        }

        if ($field->type === 'select_many') {
            $options = [];
            foreach ($field->options as $key => $labels) {
                $options[$key] = $labels[$currentLocale->code];
            }

            return new SelectManyFilter($field->id, $field->name->getCurrent($currentLocale), $options);
        }

        if ($field->type === 'one_entry') {
            return new SelectOneFilter($field->id, $field->name->getCurrent($currentLocale), []);
        }

        if ($field->type === 'many_entries') {
            return new ManyEntriesFilter($field->id, $field->name->getCurrent($currentLocale), []);
        }
    }
}
