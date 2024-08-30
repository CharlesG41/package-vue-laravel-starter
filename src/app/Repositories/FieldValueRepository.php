<?php

namespace Cyvian\Src\app\Repositories;

use Cyvian\Src\App\Models\Cyvian\FieldValue as EloquentFieldValue;
use Illuminate\Database\Eloquent\Collection;

class FieldValueRepository
{
    public function getFieldValuesByFieldId(int $fieldId): Collection
    {
        return EloquentFieldValue::where('field_id', $fieldId)->get();
    }

    public function getFieldValuesByFieldIdAndEntryId(int $fieldId, int $entryId): Collection
    {
        return EloquentFieldValue::where('field_id', $fieldId)->where('entry_id', $entryId)->get();
    }

    public function getFieldValuesByFieldIdAndLocaleIdAndEntryId(int $fieldId, int $localeId, int $entryId): Collection
    {
        return EloquentFieldValue::where('field_id', $fieldId)->where('locale_id', $localeId)->get();
    }

    public function getFieldValuesByFieldValueId(int $fieldValueId): Collection
    {
        return EloquentFieldValue::where('field_value_id', $fieldValueId)->get();
    }

    public function createFieldValue(
        ?int $fieldId,
        ?int $fieldValueId,
        int $entryId,
        ?int $localeId,
        ?string $value
    ): EloquentFieldValue
    {
        return EloquentFieldValue::create([
            'value' => $value,
            'field_id' => $fieldId,
            'entry_id' => $entryId,
            'field_value_id' => $fieldValueId,
            'locale_id' => $localeId
        ]);
    }

    public function deleteFieldValuesByFieldId(int $fieldId)
    {
        EloquentFieldValue::where('field_id', $fieldId)->delete();
    }

    public function deleteFieldValuesByFieldIdAndEntryId(int $fieldId, int $entryId)
    {
        EloquentFieldValue::where('field_id', $fieldId)->where('entry_id', $entryId)->delete();
    }

    public function deleteFieldValuesByFieldValueId(int $fieldValueId)
    {
        EloquentFieldValue::where('field_value_id', $fieldValueId)->delete();
    }
}
