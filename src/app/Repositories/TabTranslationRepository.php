<?php

namespace Cyvian\Src\app\Repositories;

use Cyvian\Src\App\Models\Translations\TabTranslation as EloquentTabTranslation;

class TabTranslationRepository
{
    public function getTabTranslationsByTabId(int $tabId)
    {
        return EloquentTabTranslation::where('parent_id', $tabId)->get();
    }

    public function createTabTranslation(string $label,  int $tabId, int $localeId): EloquentTabTranslation
    {
        return EloquentTabTranslation::create([
            'label' => $label,
            'parent_id' => $tabId,
            'locale_id' => $localeId
        ]);
    }

    public function updateTabTranslation(int $id, string $label, int $localeId): int
    {
        return EloquentTabTranslation::where('id', $id)->where('locale_id', $localeId)->update([
            'label' => $label,
        ]);
    }

    public function deleteTabTranslationByTabId(int $tabId)
    {
        return EloquentTabTranslation::where('parent_id', $tabId)->delete();
    }
}
