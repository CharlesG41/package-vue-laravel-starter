<?php

namespace Cyvian\Src\App\Utils;

use Cyvian\Src\App\Models\Cyvian\Locale;
use Cyvian\Src\App\Models\Cyvian\Tab;
use Cyvian\Src\App\Models\Cyvian\Translations\TabTranslation;

class TabHelper
{
    static public function createTab(array $tab, int $entityId, string $entityType): Tab
    {
        $locales = config('locales.locales');
        $parent = Tab::create([
            'entity_id' => $entityId,
            'entity_type' => $entityType,
        ]);
        foreach ($locales as $locale) {
            TabTranslation::create([
                'label' => $tab[$locale->code],
                'locale_id' => $locale->id,
                'parent_id' => $parent->id
            ]);
        }

        return $parent;
    }

    static public function createTabs(array $tabs, int $entityId, string $entityType): array
    {
        $tabEntries = [];
        foreach ($tabs as $tab) {
            $tabEntries[] = self::createTab($tab['labels'], $entityId, $entityType);
        }
        return $tabEntries;
    }

    static public function createTabsFromForm(array $tabs, int $entityId, $entityClass): array
    {
        $tabEntries = [];
        foreach ($tabs as $tab) {
            $tabEntries[] =  [
                'entry' => self::createTab($tab['labels'], $entityId, $entityClass),
                'random_id' => $tab['id']
            ];
        }
        return $tabEntries;
    }
}
