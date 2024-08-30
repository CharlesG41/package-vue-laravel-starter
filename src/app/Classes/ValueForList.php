<?php

namespace Cyvian\Src\app\Classes;

class ValueForList implements \JsonSerializable
{
    private $original;
    private $sanitized;

    public function __construct($original, $sanitized)
    {
        $this->original = $original;
        $this->sanitized = $sanitized;
    }

    public function jsonSerialize(): array
    {
        return [
            'original' => $this->original,
            'sanitized' => $this->sanitized
        ];
    }
}
