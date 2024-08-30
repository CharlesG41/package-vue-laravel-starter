<?php

namespace Cyvian\Src\app\Classes\Filters;

class BooleanFilter
{
    public $key;
    public $label;
    public $options;

    public function __construct(string $key, string $label)
    {
        $this->key = $key;
        $this->label = $label;
    }

    public function jsonSerialize(): array
    {
        return [
            'key' => $this->key,
            'label' => $this->label,
            'type' => 'Boolean'
        ];
    }
}
