<?php

namespace Cyvian\Src\app\Handlers\Tab;

class CreateOrUpdateTab
{
    public function __construct()
    {

    }

    public function handle()
    {
        if ($tab->id === null) {
            $tab->setEntityId($form->entityId);
            $tab->setEntityType($form->entityType);
            $tabFieldIds = $createTab->handle($form, $tab, $localesByCode);
        } else {
            $tabFieldIds = $updateTab->handle($tab, $localesByCode);
        }
        $updatedFieldIds = array_merge($updatedFieldIds, $tabFieldIds);
    }
}
