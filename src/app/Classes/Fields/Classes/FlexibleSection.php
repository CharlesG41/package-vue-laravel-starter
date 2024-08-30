<?php

namespace Cyvian\Src\app\Classes\Fields\Classes;

use Cyvian\Src\app\Utils\Localisation;

class FlexibleSection implements \JsonSerializable
{
    public $id;
    public $key;
    public $labels;
    public $fields;
    public $fieldId;

    public function __construct(string $key, Localisation $labels, array $fields)
    {
        $this->key = $key;
        $this->labels = $labels;
        $this->fields = $fields;

        foreach ($fields as $field) {
            if (!($field instanceof FieldInterface)) {
                throw new \Exception('Field must implement FieldInterface');
            }
        }
    }

    public function setId(int $id)
    {
        $this->id = $id;
    }

    public function setFieldId(int $fieldId)
    {
        $this->fieldId = $fieldId;
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'key' => $this->key,
            'labels' => $this->labels,
            'fields' => $this->fields,
            'fieldId' => $this->fieldId
        ];
    }
}
