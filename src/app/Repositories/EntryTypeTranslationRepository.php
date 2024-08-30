<?php

namespace Cyvian\Src\app\Repositories;

use Cyvian\Src\App\Models\Cyvian\Translations\EntryTypeTranslation;

class EntryTypeTranslationRepository
{
    public function getEntryTypeTranslationsByEntryTypeId(int $entryTypeId)
    {
        return EntryTypeTranslation::where('parent_id', $entryTypeId)->get();
    }

    public function createEntryTypeTranslation(string $singularName, string $pluralName, int $parentId, int $localeId): EntryTypeTranslation
    {
        return EntryTypeTranslation::create([
            'singular_name' => $singularName,
            'plural_name' => $pluralName,
            'parent_id' => $parentId,
            'locale_id' => $localeId
        ]);
    }

    public function updateEntryTypeTranslation(int $entryTypeId, string $singularName, string $pluralName, int $localeId): EntryTypeTranslation
    {
        $entryTypeTranslation = EntryTypeTranslation::where('parent_id', $entryTypeId)
            ->where('locale_id', $localeId)
            ->first();

        EntryTypeTranslation::where('parent_id', $entryTypeId)
            ->where('locale_id', $localeId)
            ->update([
                'singular_name' => $singularName,
                'plural_name' => $pluralName
            ]);

        return $entryTypeTranslation;
    }
}
