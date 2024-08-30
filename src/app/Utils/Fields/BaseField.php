<?php

namespace Cyvian\Src\App\Utils\Fields;

use Cyvian\Src\App\Models\Cyvian\Entry;
use Cyvian\Src\App\Models\Cyvian\Field;
use Cyvian\Src\App\Models\Cyvian\FieldValue;
use Cyvian\Src\App\Models\Cyvian\Locale;

class BaseField
{
    static protected function simpleStore(Field $field, $value, Entry $entry, FieldValue $fieldValue = null): void
    {
        $locales = config('locales.locales');
        if ($field->translatable) {
            foreach ($locales as $locale) {
                try {
                    self::storeValue($value[$locale->code], $entry->id, $field->id, $locale->id, $fieldValue->id ?? null);
                } catch (\Throwable $th) {
                }
            }
        } else {
            self::storeValue($value, $entry->id, $field->id, null, $fieldValue->id ?? null);
        }
    }

    static protected function storeValue(?string $value, int $entryId, int $fieldId, int $localeId = null, int $fieldValueId = null): FieldValue
    {
        return FieldValue::create([
            'value' => $value,
            'entry_id' => $entryId,
            'field_value_id' => $fieldValueId,
            'field_id' => $fieldId,
            'locale_id' => $localeId,
        ]);
    }
}
