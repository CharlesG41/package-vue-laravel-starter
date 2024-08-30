<?php

namespace Cyvian\Src\app\Classes\Translations;

use Cyvian\Src\app\Utils\Localisation;

class MenuSectionTranslation
{
    public $id;
    public $names;

    public function __construct(
        Localisation $names
    )
    {
        $this->names = $names;
    }

    public function setId(int $id)
    {
        $this->id = $id;
    }
}
