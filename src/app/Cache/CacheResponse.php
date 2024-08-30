<?php

namespace Cyvian\Src\app\Cache;

class CacheResponse
{
    public $data;
    public $foundInCache;

    public function __construct($data, bool $foundInCache)
    {
        $this->data = $data;
        $this->foundInCache = $foundInCache;
    }
}
