<?php

namespace Cyvian\Src\App\Utils\Fields;

use Cyvian\Src\App\Models\Cyvian\Entry;
use Cyvian\Src\App\Models\Cyvian\Field;
use Cyvian\Src\App\Models\Cyvian\FieldValue;
use Cyvian\Src\App\Models\Cyvian\Locale;
use Illuminate\Support\Facades\App;

class Boolean extends BaseField implements FieldInterface
{

    static public function store(Field $field, string $key, $value, Entry $entry, FieldValue $fieldValue = null): void
    {
        parent::simpleStore($field, $value ? 1 : 0, $entry, $fieldValue);
    }

    static public function value(Entry $entry, FieldValue $fieldValue)
    {
        return $fieldValue->value == 1 ? true : false;
    }

    static public function valueWithTranslations(Entry $entry, FieldValue $fieldValue)
    {
        $values = FieldValue::where('entry_id', $entry->id)->where('field_id', $fieldValue->field_id)->get();
        if ($fieldValue->translatable) {
            return $values->mapWithKeys(function ($fieldValue) {
                return [$fieldValue->locale->code => (int)$fieldValue->value];
            });
        }

        return (int)$fieldValue->value;
    }

    static public function valueForList($value, int $fieldId)
    {
        return [
            'original' => $value,
            'sanitized' => $value ? __('cyvian.general.true') : __('cyvian.general.false')
        ];
    }

    static public function isValid($value, $field)
    {
        return [
            false,
            null
        ];
    }
}
