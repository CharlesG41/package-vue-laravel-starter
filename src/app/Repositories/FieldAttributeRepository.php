<?php

namespace Cyvian\Src\app\Repositories;

use Cyvian\Src\App\Models\Cyvian\FieldAttribute;
use Illuminate\Database\Eloquent\Collection;

class FieldAttributeRepository
{
    public function getTypeAttributeFromFieldId(int $fieldId): string
    {
        return FieldAttribute::where('field_id', $fieldId)->where('key', 'type')->first()->value;
    }

    public function getFieldAttributesByFieldId(int $fieldId): Collection
    {
        return FieldAttribute::where('field_id', $fieldId)->get();
    }

    public function createFieldAttribute(string $key, string $value, int $fieldId): FieldAttribute
    {
        return FieldAttribute::create([
            'key' => $key,
            'value' => $value,
            'field_id' => $fieldId
        ]);
    }

    public function bulkInsertFieldAttributes(array $fieldAttributesAsArray): bool
    {
        return FieldAttribute::insert($fieldAttributesAsArray);
    }

    public function deleteFieldAttributesByFieldId(int $fieldId): void
    {
        FieldAttribute::where('field_id', $fieldId)->delete();
    }
}
