<?php

namespace Cyvian\Src\app\Handlers\Utils;

class MergeValueIntoField
{
    public function handle(array $values, array $fields): array
    {
        foreach($values as $key => $value) {
            foreach($fields as $field) {
                if($field->key === $key) {
//                    if ($field->key == 'repeater') {
//                        dd($field, $value);
//                    }
                    $field->setValue($value);
                }
            }
        }

        return $fields;
    }
}
