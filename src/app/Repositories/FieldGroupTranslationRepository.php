<?php

namespace Cyvian\Src\app\Repositories;

use Cyvian\Src\App\Models\Cyvian\Translations\FieldGroupTranslation as EloquentFieldGroupTranslation;

class FieldGroupTranslationRepository
{
    public function getFieldGroupTranslationsByFieldGroupId(int $fieldGroupId)
    {
        return EloquentFieldGroupTranslation::where('parent_id', $fieldGroupId)->get();
    }
}
