<?php

namespace Cyvian\Src\app\Classes;

use Cyvian\Src\app\Classes\Fields\Classes\EntryType;
use Cyvian\Src\app\Utils\Localisation;

class MenuItem implements \JsonSerializable
{
    public $names;
    public $url;

    public function __construct(
        Localisation $names,
        string $url
    )
    {
        $this->names = $names;
        $this->url = $url;
    }

    public function jsonSerialize()
    {
        return [
            'name' => $this->names->getCurrent(),
            'url' => $this->url
        ];
    }
}
