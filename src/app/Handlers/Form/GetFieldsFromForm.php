<?php

namespace Cyvian\Src\app\Handlers\Form;

use Cyvian\Src\app\Classes\Form;

class GetFieldsFromForm
{
    public function handle(Form $form): array
    {
        $fields = [];
        foreach ($form->sections as $section) {
            $fields = array_merge($fields, $section->fields);
        }

        return $fields;
    }
}
