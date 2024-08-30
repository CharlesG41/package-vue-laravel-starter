<?php

namespace Cyvian\Src\app\Handlers\Utils;

class SnakeToCamel
{
    public function handle(string $snakeCase): string
    {
        return lcfirst(str_replace('_', '', ucwords($snakeCase, '_')));
    }
}
