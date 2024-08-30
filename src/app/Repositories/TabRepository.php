<?php

namespace Cyvian\Src\app\Repositories;

use Cyvian\Src\app\Classes\Tab;
use Cyvian\Src\App\Models\Cyvian\Tab as EloquentTab;

class TabRepository
{
    public function getTabsByEntityIdAndEntityType(int $entityId, string $entityType)
    {
        return EloquentTab::where('entity_id', $entityId)
            ->where('entity_type', $entityType)
            ->get();
    }

    public function createTab(Tab $tab)
    {
        return EloquentTab::create([
            'entity_id' => $tab->entityId,
            'entity_type' => $tab->entityType,
        ]);
    }

    public function deleteTabsByEntityIdAndEntityType(int $entityId, string $entityType)
    {
        return EloquentTab::where('entity_id', $entityId)
            ->where('entity_type', $entityType)
            ->delete();
    }

    public function deleteTabById(int $tabId)
    {
        return EloquentTab::where('id', $tabId)
            ->delete();
    }
}
