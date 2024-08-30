<?php

namespace Cyvian\Src\app\Repositories;

use Cyvian\Src\app\Cache\RoleCache;
use Cyvian\Src\App\Models\Role;

class RoleRepository
{
    public function getAllRoles()
    {

        $cacheResponse = RoleCache::getAllRoles();
        if($cacheResponse->foundInCache) {
            return $cacheResponse->data;
        }
        $roles = Role::all();

        RoleCache::setAllRoles($roles);

        return ;
    }
}
