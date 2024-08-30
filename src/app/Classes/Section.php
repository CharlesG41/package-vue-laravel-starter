<?php

namespace Cyvian\Src\app\Classes;

use Cyvian\Src\app\Classes\Fields\Classes\FieldInterface;
use Cyvian\Src\app\Classes\Translations\SectionTranslation;

class Section implements \JsonSerializable
{
    CONST POSITION_RIGHT = 'right';
    CONST POSITION_LEFT = 'left';

    public $id;
    public $translation;
    public $fields;
    public $key;
    public $position;
    public $isBaseSection;
    public $entityId;
    public $entityType;

    public function __construct(
        SectionTranslation $sectionTranslation,
        ?string $key,
        string $position,
        array $fields
    )
    {
        $this->translation = $sectionTranslation;
        $this->key = $key;
        $this->position = $position;
        $this->fields = $fields;

        if ($position !== self::POSITION_RIGHT && $position !== self::POSITION_LEFT) {
            throw new \InvalidArgumentException('Position must be either right or left');
        }
        foreach($fields as $field) {
            if(!$field instanceof FieldInterface) {
                throw new \InvalidArgumentException('Field '. $field->key .' must be an instance of FieldInterface');
            }
        }
    }

    public function setId(int $id)
    {
        $this->id = $id;
        $this->translation->setSectionId($this->id);
    }

    public function setEntityId(string $entityId)
    {
        $this->entityId = $entityId;
    }

    public function setEntityType(string $entityType)
    {
        $this->entityType = $entityType;
    }

    public function setIsBaseSection(bool $isBaseSection)
    {
        $this->isBaseSection = $isBaseSection;
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'labels' => $this->translation->labels,
            'key' => $this->key,
            'fields' => $this->fields,
            'isBaseSection' => $this->isBaseSection,
            'position' => $this->position,
            'entityId' => $this->entityId,
            'entityType' => $this->entityType,
        ];
    }
}
