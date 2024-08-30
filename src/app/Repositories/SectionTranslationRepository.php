<?php

namespace Cyvian\Src\app\Repositories;

use Cyvian\Src\app\Classes\Translations\SectionTranslation;
use Cyvian\Src\App\Models\Cyvian\Translations\SectionTranslation as EloquentSectionTranslation;

class SectionTranslationRepository
{
    public function getSectionTranslationsBySectionId(int $sectionId)
    {
        return EloquentSectionTranslation::where('parent_id', $sectionId)->get();
    }

    public function createSectionTranslation(string $label, int $parentId, int $localeId): EloquentSectionTranslation
    {
        return EloquentSectionTranslation::create([
            'label' => $label,
            'parent_id' => $parentId,
            'locale_id' => $localeId,
        ]);
    }

    public function updateSectionTranslation(int $id, string $label, int $localeId): int
    {
        return EloquentSectionTranslation::where('id', $id)->where('locale_id', $localeId)->update([
            'label' => $label,
        ]);
    }

    public function deleteSectionTranslationBySectionId(int $sectionId)
    {
        EloquentSectionTranslation::where('parent_id', $sectionId)->delete();
    }
}
