<?php

namespace Cyvian\Src\app\Classes\Translations;

use Cyvian\Src\app\Utils\Localisation;

class FieldGroupTranslation
{
    public $names;
    public function __construct(
        Localisation $names
    )
    {
        $this->names = $names;
    }
}
