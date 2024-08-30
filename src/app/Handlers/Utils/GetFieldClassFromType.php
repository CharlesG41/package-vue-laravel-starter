<?php

namespace Cyvian\Src\app\Handlers\Utils;

class GetFieldClassFromType
{
    public function handle(string $type): string
    {
        return '\\Cyvian\\Src\\App\\Classes\\Fields\\' . ucfirst($this->snakeToCamel($type));
    }

    private function snakeToCamel(string $string): string
    {
        return str_replace(' ', '', ucwords(str_replace('_', ' ', $string)));
    }
}
