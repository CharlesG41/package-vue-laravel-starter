<?php

namespace Cyvian\Src\app\Handlers\Utils;

class SanitizeFieldArrayData
{
    public function handle(array $data)
    {
        return array_merge($data, [
            'key' => $data['key'] ?? '',
            'name' => $data['name'] ?? null,
            'description' => $data['description'] ?? null
        ]);
    }
}
