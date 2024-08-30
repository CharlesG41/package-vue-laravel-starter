<?php

namespace Cyvian\Src\app\Classes\Translations;

use Cyvian\Src\app\Utils\Localisation;

class EntryTypeTranslation
{
    public $id;
    public $singularNames;
    public $pluralNames;

    public function __construct(Localisation $singularNames, Localisation $pluralNames)
    {
        $this->singularNames = $singularNames;
        $this->pluralNames = $pluralNames;
    }

    public function setId(int $id)
    {
        $this->id = $id;
    }

    public function setTranslationId(int $id)
    {
        $this->setId($id);
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'singularNames' => $this->singularNames,
            'pluralNames' => $this->pluralNames,
        ];
    }
}
