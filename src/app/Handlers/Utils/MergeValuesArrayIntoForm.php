<?php

namespace Cyvian\Src\app\Handlers\Utils;

use Cyvian\Src\app\Classes\Form;

class MergeValuesArrayIntoForm
{
    // This function is used to merge values from an array into a form
    public function handle(array $values, Form $form): Form
    {
        $mergeValueIntoField = new MergeValueIntoField();
        foreach($form->sections as $section) {
            if ($section->key === null) {
                $section->fields = $mergeValueIntoField->handle($values, $section->fields);
            } else {
                $section->fields = $mergeValueIntoField->handle($values[$section->key], $section->fields);
            }
        }
        return $form;
    }
}
