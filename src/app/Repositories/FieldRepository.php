<?php

namespace Cyvian\Src\app\Repositories;

use Cyvian\Src\app\Classes\Fields\Classes\FieldPermissions;
use Cyvian\Src\App\Models\Cyvian\Field as EloquentField;
use Cyvian\Src\app\Utils\Localisation;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class FieldRepository
{
    public function getFieldById(int $fieldId): EloquentField
    {
        return EloquentField::find($fieldId);
    }

    public function getFieldsByEntityIdAndEntityType(int $entityId, string $entityType, ?int $entryId): Collection
    {
        return EloquentField::where('entity_id', $entityId)
            ->where('entity_type', $entityType)
            ->where(function($query) use ($entryId) {
                $query->whereNull('entry_id')
                    ->orWhere('entry_id', $entryId);
            })
            ->get();
    }

    public function getFieldIdsByEntityIdAndEntityType(int $entityId, string $entityType): array
    {
        return DB::table('fields')
            ->where('entity_id', $entityId)
            ->where('entity_type', $entityType)
            ->pluck('id')
            ->toArray();
    }

    public function createField(
        string $key,
        string $type,
        Localisation $name,
        Localisation $description,
        int $width,
        bool $translatable,
        bool $displayOnList,
        bool $hasFilter,
        bool $locked,
        array $conditions,
        FieldPermissions $fieldPermission,
        bool $isBaseField,
        ?int $entryId,
        int $entityId,
        string $entityType
    ): EloquentField
    {
        return EloquentField::create([
            'key' => $key,
            'type' => $type,
            'name' => json_encode($name),
            'description' => json_encode($description),
            'translatable' => $translatable,
            'display_on_list' => $displayOnList,
            'has_filter' => $hasFilter,
            'width' => $width,
            'locked' => $locked,
            'conditions' => json_encode($conditions),
            'hidden_on_create' => $fieldPermission->hiddenOnCreate,
            'hidden_on_edit' => $fieldPermission->hiddenOnEdit,
            'disabled_on_edit' => $fieldPermission->disabledOnEdit,
            'roles_on_create' => json_encode($fieldPermission->rolesOnCreate),
            'roles_on_edit_or_disable' => json_encode($fieldPermission->rolesOnEditOrDisable),
            'roles_on_edit_or_hide' => json_encode($fieldPermission->rolesOnEditOrHide),
            'is_base_field' => $isBaseField,
            'entry_id' => $entryId,
            'entity_id' => $entityId,
            'entity_type' => $entityType
        ]);
    }

    public function updateField(
        int $id,
        string $key,
        string $type,
        Localisation $name,
        Localisation $description,
        bool $translatable,
        bool $displayOnList,
        bool $hasFilter,
        int $width,
        bool $locked,
        array $conditions,
        FieldPermissions $fieldPermission
    ): int
    {
        return EloquentField::where('id', $id)->update([
            'key' => $key,
            'type' => $type,
            'name' => json_encode($name),
            'description' => json_encode($description),
            'translatable' => $translatable,
            'width' => $width,
            'display_on_list' => $displayOnList,
            'has_filter' => $hasFilter,
            'locked' => $locked,
            'conditions' => json_encode($conditions),
            'hidden_on_create' => $fieldPermission->hiddenOnCreate,
            'hidden_on_edit' => $fieldPermission->hiddenOnEdit,
            'disabled_on_edit' => $fieldPermission->disabledOnEdit,
            'roles_on_create' => json_encode($fieldPermission->rolesOnCreate),
            'roles_on_edit_or_disable' => json_encode($fieldPermission->rolesOnEditOrDisable),
            'roles_on_edit_or_hide' => json_encode($fieldPermission->rolesOnEditOrHide),
        ]);
    }

    public function deleteFieldById(int $fieldId)
    {
        EloquentField::where('id', $fieldId)->delete();
    }

    public function deleteFieldsByEntityIdAndEntityType(int $entityId, string $entityType): array
    {
        $deletedIds = DB::table('fields')
            ->select('id')
            ->where('entity_id', $entityId)
            ->where('entity_type', $entityType)
            ->get()
            ->toArray();
        EloquentField::where('entity_id', $entityId)
            ->where('entity_type', $entityType)
            ->delete();

        return $deletedIds;
    }
}
