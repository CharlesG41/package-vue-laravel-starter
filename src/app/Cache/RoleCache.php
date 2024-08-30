<?php

namespace Cyvian\Src\app\Cache;

use Illuminate\Support\Facades\Cache;

class RoleCache
{
    public function getAllRoles(): CacheResponse
    {
//        $data = Cache::get($key );

        return new CacheResponse(
            null,
            false
        );
    }

    public function setAllRoles(): void
    {
        Cache::set()
    }
}
