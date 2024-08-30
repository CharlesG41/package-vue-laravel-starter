<?php

namespace Cyvian\Src\app\Classes;

use Cyvian\Src\app\Classes\Fields\Classes\FieldInterface;
use Cyvian\Src\app\Classes\Translations\TabTranslation;

class Tab implements \JsonSerializable
{
    public $id;
    public $translation;
    public $fields;
    public $entityId;
    public $entityType;
    public $isBaseTab;

    public function __construct(
        TabTranslation $translations,
        array $fields
    )
    {
        $this->translation = $translations;
        $this->fields = $fields;

        foreach($fields as $field) {
            if(!$field instanceof FieldInterface) {
                throw new \InvalidArgumentException('Field '. $field->key .' must be an instance of FieldInterface');
            }
        }
    }

    public function setId(int $id)
    {
        $this->id = $id;
        $this->translation->setTabId($id);
    }

    public function setEntityId(int $entityId)
    {
        $this->entityId = $entityId;
    }

    public function setEntityType(string $entityType)
    {
        $this->entityType = $entityType;
        $this->setIsBaseTab($entityType === EntryType::class);
    }

    public function setIsBaseTab(bool $isBaseTab)
    {
        $this->isBaseTab = $isBaseTab;
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'labels' => $this->translation->labels,
            'fields' => $this->fields,
            'entityId' => $this->entityId,
            'entityType' => $this->entityType,
            'isBaseTab' => $this->isBaseTab
        ];
    }
}
