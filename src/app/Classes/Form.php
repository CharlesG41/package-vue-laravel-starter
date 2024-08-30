<?php

namespace Cyvian\Src\app\Classes;

use Cyvian\Src\app\Classes\Fields\Classes\BaseField;
use Cyvian\Src\app\Classes\Fields\Classes\FieldInterface;

class Form implements \JsonSerializable
{
    public $sections;
    public $entityId;
    public $entityType;
    public $hasError;
    public $localesAffected;
    public $isValidated = false;

    public function __construct(
        array $sections
    )
    {
        $this->sections = $sections;

        foreach($sections as $section) {
            if(!$section instanceof Section) {
                throw new \Exception('Section must be an instance of Section');
            }
        }
    }

    public function jsonSerialize(): array
    {
        return [
            'sections' => $this->sections,
            'entityId' => $this->entityId,
            'entityType' => $this->entityType,
            'hasError' => $this->hasError,
            'localeAffected' => $this->localesAffected
        ];
    }

    public function setEntityId(int $entityId)
    {
        $this->entityId = $entityId;
    }

    public function setEntityType(string $entityType)
    {
        $this->entityType = $entityType;
    }

    public function setHasError(bool $hasError)
    {
        $this->hasError = $hasError;
        $this->isValidated = true;
    }

    public function setLocalesAffected(array $localesAffected)
    {
        $this->localesAffected = $localesAffected;
    }
}
