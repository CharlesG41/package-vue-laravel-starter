<?php

namespace Cyvian\Src\App\Utils;

use Cyvian\Src\App\Models\Cyvian\EntryType;
use Cyvian\Src\App\Models\Cyvian\FieldGroup;

class FieldGroupHelper
{
    static public function getFieldGroups(EntryType $entryType): array
    {
        return array_merge(
            $entryType->fieldGroups->map(function ($fieldGroup) {
                $fields = array_map(function ($field) {
                    unset($field['id']);
                    return $field;
                }, $fieldGroup->fields->toArray());

                return [
                    'id' => $fieldGroup->id,
                    'name' => $fieldGroup->translation->name,
                    'fields' => $fields
                ];
            })->all(),
            FieldGroup::whereDoesntHave('entryTypes')->get()->map(function ($fieldGroup) {
                $fields = array_map(function ($field) {
                    unset($field['id']);
                    return $field;
                }, $fieldGroup->fields->toArray());

                return [
                    'id' => $fieldGroup->id,
                    'name' => $fieldGroup->translation->name,
                    'fields' => $fields
                ];
            })->all()
        );
    }
}
