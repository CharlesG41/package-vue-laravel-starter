<?php

namespace Cyvian\Src\app\Handlers\Field;

class GetFieldClassFromFieldType
{
    public function handle(string $fieldType): string
    {
        return 'Cyvian\Src\app\Classes\Fields\\' . $fieldType;
    }
}
