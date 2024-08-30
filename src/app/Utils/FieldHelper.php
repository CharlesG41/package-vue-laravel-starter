<?php

namespace Cyvian\Src\App\Utils;

use Cyvian\Src\App\Models\Cyvian\Entry;
use Cyvian\Src\App\Models\Cyvian\Field;
use Cyvian\Src\App\Models\Cyvian\FieldAttribute;
use Cyvian\Src\App\Models\Cyvian\FieldValue;
use Illuminate\Support\Collection;

class FieldHelper
{
    static public function getFieldClass(string $type): string
    {
        return '\\Cyvian\\Src\\App\\Utils\\Fields\\' . ucfirst(Helper::snakeToCamel($type));
    }

    static public function createFields(array $fields, int $entityId, string $entityType): array
    {
        $fieldEntries = [];
        foreach ($fields as $field) {
            $fieldEntries[] = self::createField($field, $entityId, $entityType);
        }
        return $fieldEntries;
    }

    static public function createField(array $attributes, int $entityId, string $entityType): Field
    {
        $field = Field::create([
            'entity_id' => $entityId,
            'entity_type' => $entityType,
        ]);
        foreach ($attributes as $key => $value) {
            self::createFieldAttributes($field->id, $key, $value, false);
        }
        return $field;
    }

    static private function createFieldAttributes(int $fieldId, string $key, $value, bool $keepId): void
    {
        $needToBeEncoded = [
            'name',
            'options',
            'description',
            'entry_types',
            'roles_on_create',
            'roles_on_edit_or_disable',
            'roles_on_edit_or_hide',
            'conditions',
            'regex_message'
        ];
        if ($key === 'default' && is_array($value)) {
            $value = json_encode($value);
        }

        if ($key === 'fields' || $key === 'sections') {
            $fields = $value;
            foreach ($fields as $field) {
                $newId = Field::create([
                    'entity_id' => $fieldId,
                    'entity_type' => Constant::CLASS_NAME_CYVIAN_SRC_APP_MODELS_CYVIAN . 'Field',
                ])->id;
                foreach ($field as $key => $v) {
                    self::createFieldAttributes($newId, $key, $v, false);
                }
            }
        } elseif (in_array($key, $needToBeEncoded)) {
            FieldAttribute::create([
                'key' => $key,
                'value' => json_encode($value),
                'field_id' => $fieldId
            ]);
        } else {
            FieldAttribute::create([
                'key' => $key,
                'value' => $value,
                'field_id' => $fieldId
            ]);
        }
    }

    static public function createValues(array $values, Collection $fields, Entry $entry, FieldValue $fieldValue = null): void
    {
        foreach ($fields as $field) {
            $value = null;
            foreach ($values as $key => $v) {
                if ($key === $field->key) {
                    $value = $v;
                }
            }
            $type = self::getFieldClass($field->field_attributes['type']);
            $type::store($field, $key, $value, $entry, $fieldValue);
        }
    }

    static public function ajustTabsInFields(array $tabs, array $fields): array
    {
        foreach ($fields as &$field) {
            if (array_key_exists('tab', $field)) {
                foreach ($tabs as $tab) {
                    $tabId = array_key_exists('random_id', $tab) ? $tab['random_id'] : $tab['id'];
                    if ($tabId == $field['tab']) {
                        $field['tab'] = $tab['entry']->id;
                        break;
                    }
                }
            }
        }

        return $fields;
    }

    static public function updateFields(array $fields, int $entityId, string $entityType, bool $keepId = false): array
    {
        $fieldEntries = [];
        foreach ($fields as $field) {
            $fieldEntries[] = self::updateField($field, $entityId, $entityType, $keepId);
        }

        return $fieldEntries;
    }

    static public function updateField($field, int $entityId, string $entityType, bool $keepId): Field
    {
        if (array_key_exists('id', $field)) {
            Field::where('entity_id', $field['id'])->where('entity_type', Constant::CLASS_NAME_CYVIAN_SRC_APP_MODELS_CYVIAN . 'Field')->get()->each(function ($field) {
                $field->delete();
            });
            $fieldEntry = Field::find($field['id']);
            $fieldEntry->fattributes()->delete();
            foreach ($field as $key => $value) {
                self::createFieldAttributes($fieldEntry->id, $key, $value, $keepId);
            }
        } else {
            $fieldEntry = Field::create([
                'entity_id' => $entityId,
                'entity_type' => $entityType,
            ]);
            foreach ($field as $key => $value) {
                self::createFieldAttributes($fieldEntry->id, $key, $value, $keepId);
            }
        }

        return $fieldEntry;
    }
}
