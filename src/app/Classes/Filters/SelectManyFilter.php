<?php

namespace Cyvian\Src\app\Classes\Filters;

class SelectManyFilter
{
    public $key;
    public $label;
    public $options;

    public function __construct(string $key, string $label, array $options)
    {
        $this->key = $key;
        $this->label = $label;
        $this->options = $options;
    }

    public function jsonSerialize(): array
    {
        return [
            'key' => $this->key,
            'label' => $this->label,
            'options' => $this->options,
            'type' => 'SelectOne'
        ];
    }
}
