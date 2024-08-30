<?php

namespace Cyvian\Src\app\Classes;

use Cyvian\Src\app\Classes\Translations\FieldGroupTranslation;

class FieldGroup
{
    public $id;
    public $translation;
    public $fields;
    public $entryTypeIds;

    public function __construct(
        FieldGroupTranslation $translations,
        array $fields
    )
    {
        $this->translation = $translations;
        $this->fields = $fields;
    }

    public function setEntryTypeIds(array $entryTypeIds)
    {
        $this->entryTypeIds = $entryTypeIds;
    }

    public function setId(int $id)
    {
        $this->id = $id;
    }

    public function jsonSerialize(): array
    {
        return [
            'translations' => $this->translation,
            'fields' => $this->fields,
        ];
    }
}
