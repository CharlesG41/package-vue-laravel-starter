<?php

namespace Cyvian\Src\app\Repositories;

use Cyvian\Src\App\Models\Cyvian\MenuSection as EloquentMenuSection;

class MenuSectionRepository
{
    public function getMenuSections()
    {
        return EloquentMenuSection::all();
    }
}
