<?php

namespace Cyvian\Src\app\Repositories;

use Cyvian\Src\App\Models\Cyvian\MenuSection;
use Cyvian\Src\App\Models\Translations\MenuSectionTranslation;

class MenuSectionTranslationRepository
{
    public function getMenuSectionTranslationByMenuSectionId(int $menuSectionId)
    {
        return MenuSectionTranslation::where('parent_id', $menuSectionId)->get();
    }
}
