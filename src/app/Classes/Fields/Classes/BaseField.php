<?php

namespace Cyvian\Src\app\Classes\Fields\Classes;

use Cyvian\Src\app\Classes\EntryType;
use Cyvian\Src\app\Classes\ValueForList;
use Cyvian\Src\app\Handlers\Field\CreateFieldInDatabase;
use Cyvian\Src\app\Handlers\Field\UpdateFieldInDatabase;
use Cyvian\Src\app\Handlers\Utils\ValidateValueFromField;
use Cyvian\Src\App\Models\Cyvian\Field;
use Cyvian\Src\app\Repositories\FieldAttributeRepository;
use Cyvian\Src\app\Repositories\FieldRepository;
use Cyvian\Src\app\Repositories\FieldValueRepository;
use Cyvian\Src\App\Utils\Localisation;

class BaseField
{
    const POSITION_TOP = 'top';
    const POSITION_RIGHT = 'right';
    const POSITION_LEFT = 'left';
    const FIELD_CLASS = 'Cyvian\Src\app\Classes\Fields\\';

    public $id;
    public $isBaseField;
    public $key;
    public $type;
    public $name;
    public $description;
    public $translatable;
    public $displayOnList;
    public $width;
    public $locked;
    public $conditions;
    public $fieldPermissions;
    public $value;
    public $entryId;
    public $entityId;
    public $entityType;
    public $isValueSet = false;
    public $position;
    public $hasFilter;
    public $default;
    public $error;

    public function __construct(
        string $key,
        string $type,
        Localisation $name,
        ?Localisation $description,
        bool $translatable,
        bool $displayOnList,
        bool $hasFilter,
        int $width,
        bool $locked,
        array $conditions,
        FieldPermissions $fieldPermissions
    )
    {
        $this->key = $key;
        $this->type = $type;
        $this->name = $name;
        $this->description = $description;
        $this->translatable = $translatable;
        $this->displayOnList = $displayOnList;
        $this->hasFilter = $hasFilter;
        $this->width = $width;
        $this->locked = $locked;
        $this->conditions = $conditions;
        $this->fieldPermissions = $fieldPermissions;

        foreach ($conditions as $condition) {
            if (!$condition instanceof Condition) {
                throw new \InvalidArgumentException('Condition must be an instance of Condition');
            }
        }
    }

    public function setId(int $id)
    {
        $this->id = $id;
    }

    public function setEntityId(int $entityId)
    {
        $this->entityId = $entityId;
    }

    public function setEntityType(string $entityType)
    {
        $this->entityType = $entityType;
    }

    public function setPosition(?string $position): void
    {
        $this->position = $position;
    }

    public function setIsBaseField(bool $isBaseField): void
    {
        $this->isBaseField = $isBaseField;
    }

    public function setEntryId(?int $entryId)
    {
        $this->entryId = $entryId;
    }

    public function setNotTranslatableDeep()
    {
        $this->translatable = false;
    }

    static protected function instantiateFieldPermissionsFromArray(array $data): FieldPermissions
    {
        return new FieldPermissions(
            $data['hidden_on_create'],
            $data['hidden_on_edit'],
            $data['disabled_on_edit'],
            $data['roles_on_create'],
            $data['roles_on_edit_or_disable'],
            $data['roles_on_edit_or_hide']
        );
    }

    static protected function instantiateFieldPermissionsFromEloquent(Field $eloquentField): FieldPermissions
    {
        return new FieldPermissions(
            $eloquentField->hidden_on_create,
            $eloquentField->hidden_on_edit,
            $eloquentField->disabled_on_edit,
            json_decode($eloquentField->roles_on_create, true),
            json_decode($eloquentField->roles_on_edit_or_disable, true),
            json_decode($eloquentField->roles_on_edit_or_hide, true)
        );
    }

    static protected function instantiateConditionsFromArray(array $data): array
    {
        $conditions = [];
        foreach ($data as $condition) {
            $conditions[] = new Condition($condition['field'], $condition['operator'], $condition['value']);
        }
        return $conditions;
    }

    protected function baseInstantiateFromArray(array $data): void
    {
        if (array_key_exists('id', $data) && $data['id'] !== null) {
            $this->setId($data['id']);
        }

        if (array_key_exists('entity_id', $data) && $data['entity_id'] !== null) {
            $this->setEntityId($data['entity_id']);
        }

        if (array_key_exists('entity_type', $data) && $data['entity_type'] !== null) {
            $this->setEntityType($data['entity_type']);
        }

        if (array_key_exists('position', $data) && $data['position'] !== null) {
            $this->setPosition($data['position']);
        }

        if (array_key_exists('is_base_field', $data) && $data['is_base_field'] !== null) {
            $this->setIsBaseField($data['is_base_field']);
        }
    }

    protected function baseCreateFieldInDatabase(FieldRepository $fieldRepository)
    {
        if ($this->entityId === null || $this->entityType === null) {
            throw new \Exception('Cannot create field without entity id and entity type');
        }

        $eloquentField = $fieldRepository->createField(
            $this->key,
            $this->type,
            $this->name,
            $this->description,
            $this->width,
            $this->translatable,
            $this->displayOnList,
            $this->hasFilter,
            $this->locked,
            $this->conditions,
            $this->fieldPermissions,
            $this->isBaseField,
            $this->entryId,
            $this->entityId,
            $this->entityType
        );
        $this->setId($eloquentField->id);
    }

    protected function baseUpdateFieldInDatabase(FieldRepository $fieldRepository)
    {
        if ($this->id === null) {
            throw new \Exception('Cannot update field without an id');
        }

        $fieldRepository->updateField(
            $this->id,
            $this->key,
            $this->type,
            $this->name,
            $this->description,
            $this->translatable,
            $this->displayOnList,
            $this->hasFilter,
            $this->width,
            $this->locked,
            $this->conditions,
            $this->fieldPermissions,
        );
    }

    public function deleteFieldValueInDatabase(FieldValueRepository $fieldValueRepository, int $entryId)
    {
        $fieldValueRepository->deleteFieldValuesByFieldIdAndEntryId($this->id, $entryId);
    }

    public function setValue($value): void
    {
        $this->value = $value;
        $this->isValueSet = true;
    }

    public function getValueForList(string $currentLocaleCode): ValueForList
    {
        if ($this->translatable) {
            return new ValueForList($this->value[$currentLocaleCode], $this->value[$currentLocaleCode]);
        } else {
            return new ValueForList($this->value, $this->value);
        }
    }

    public function getValuesWithTranslations(array $siteLocales, $entryId)
    {
        if ($this->isValueSet) {
            return $this->value;
        } else {
            $this->setValueFromDatabase($siteLocales, $entryId);
            return $this->value;
        }
    }

    public function getTranslatedValue(string $currentLocaleCode)
    {
        if (!$this->isValueSet) {
            throw new \Exception('Cannot get value without setting a value first');
        }

        if ($this->translatable) {
            return $this->value[$currentLocaleCode];
        } else {
            return $this->value;
        }
    }

    public function setDefaultValue($defaultValue): self
    {
        $this->value = $defaultValue;
        $this->default = $defaultValue;
        $validateValueFromField = new ValidateValueFromField();
        $validationResponse = $validateValueFromField->handle($this, []);
        if (!$validationResponse->isSuccessful) {
            throw new \InvalidArgumentException($validationResponse->data);
        }

        $this->value = null;

        return $this;
    }

    protected function getBaseAttributesArray(): array
    {
        return [
            'default' => $this->default,
            'is_base_field' => $this->isBaseField,
            'type' => $this->type,
            'key' => $this->key,
            'name' => $this->name->toArray(),
            'description' => $this->description->toArray(),
            'translatable' => $this->translatable,
            'display_on_list' => $this->displayOnList,
            'has_filter' => $this->hasFilter,
            'width' => $this->width,
            'locked' => $this->locked,
            'conditions' => $this->conditions,
            'hidden_on_create' => $this->fieldPermissions->hiddenOnCreate,
            'hidden_on_edit' => $this->fieldPermissions->hiddenOnEdit,
            'disabled_on_edit' => $this->fieldPermissions->disabledOnEdit,
            'roles_on_create' => $this->fieldPermissions->rolesOnCreate,
            'roles_on_edit_or_disable' => $this->fieldPermissions->rolesOnEditOrDisable,
            'roles_on_edit_or_hide' => $this->fieldPermissions->rolesOnEditOrHide,
            'id' => $this->id,
            'entity_id' => $this->entityId,
            'entity_type' => $this->entityType,
            'error' => $this->error,
        ];
    }
}
