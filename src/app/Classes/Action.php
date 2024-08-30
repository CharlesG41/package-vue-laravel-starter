<?php

namespace Cyvian\Src\app\Classes;

use Cyvian\Src\app\Classes\Fields\Classes\FieldInterface;
use Cyvian\Src\app\Classes\Translations\ActionTranslation;

class Action implements \JsonSerializable
{
    public const ACTION_TYPE_VIEW = 'view';
    public const ACTION_TYPE_EXECUTE = 'execute';
    public const ACTION_TYPE_DOWNLOAD = 'download';
    public const ACTION_TYPE_ADMIN = 'admin';
    public const POSITION_TOP = 'top';
    public const POSITION_ROW = 'row';
    public const POSITION_GENERAL = 'general';

    public const FIELD_KEY = '__action_';

    public $id;
    public $entryTypeId;
    public $name;
    public $position;
    public $actionType;
    public $url;
    public $rolesByEntry;
    public $translation;
    public $roleIds;
    public $fields;

    public function __construct(
        string $name,
        string $position,
        string $actionType,
        ?string $url,
        ?bool $rolesByEntry,
        ?ActionTranslation $actionTranslation,
        array $roleIds,
        array $fields
    )
    {
        $this->name = $name;
        $this->position = $position;
        $this->actionType = $actionType;
        $this->url = $url;
        $this->rolesByEntry = $rolesByEntry;
        $this->roleIds = $roleIds;
        $this->fields = $fields;
        $this->translation = $actionTranslation;

        foreach($fields as $field) {
            if(!$field instanceof FieldInterface) {
                throw new \Exception('Field must be an instance of FieldInterface');
            }
        }

        if($actionType != self::ACTION_TYPE_ADMIN && $actionType != self::ACTION_TYPE_VIEW && $actionType != self::ACTION_TYPE_EXECUTE && $actionType != self::ACTION_TYPE_DOWNLOAD) {
            throw new \Exception('Invalid action type');
        }
    }

    public function setId(int $id)
    {
        $this->id = $id;
        if ($this->translation) {
            $this->translation->setActionId($id);
        }
    }

    public function setEntryTypeId(int $entryTypeId)
    {
        $this->entryTypeId = $entryTypeId;
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'entry_type_id' => $this->entryTypeId,
            'name' => $this->name,
            'position' => $this->position,
            'action_type' => $this->actionType,
            'url' => $this->url,
            'roles_by_entry' => $this->rolesByEntry,
            'role_ids' => $this->roleIds,
            'fields' => $this->fields,
            'label' => $this->translation->labels->getCurrent(),
            'action_label' => $this->translation->actionLabels->getCurrent()
        ];
    }

    public function toArray()
    {
        return [
            'id' => $this->id,
            'entry_type_id' => $this->entryTypeId,
            'name' => $this->name,
            'position' => $this->position,
            'action_type' => $this->actionType,
            'url' => $this->url,
            'roles_by_entry' => $this->rolesByEntry,
            'role_ids' => $this->roleIds,
            'fields' => $this->fields,
            'label' => $this->translation->labels->toArray(),
            'action_label' => $this->translation->actionLabels->toArray()
        ];
    }
}
