<?php

namespace Cyvian\Src\app\Handlers\Tab;

use Cyvian\Src\app\Classes\Tab;
use Cyvian\Src\app\Handlers\Field\InstantiateFieldFromArray;
use Cyvian\Src\app\Handlers\Tab\TabTranslation\InstantiateTabTranslationFromArray;

class InstantiateTabFromArray
{
    public function handle(array $tabArray)
    {
        $instantiateFieldFromArray = new InstantiateFieldFromArray;
        $instantiateTabTranslationFromArray = new InstantiateTabTranslationFromArray;

        $fields = [];
        foreach ($tabArray['fields'] as $field) {
            $fields[] = $instantiateFieldFromArray->handle($field);
        }

        $translations = $instantiateTabTranslationFromArray->handle($tabArray);

        $tab = new Tab(
            $translations,
            $fields
        );

        if (array_key_exists('id', $tabArray)) {
            $tab->setId($tabArray['id']);
        }

        if (array_key_exists('entityId', $tabArray)) {
            $tab->setEntityId($tabArray['entityId']);
        }

        if (array_key_exists('entityType', $tabArray)) {
            $tab->setEntityType($tabArray['entityType']);
        }

        if (array_key_exists('isBaseTab', $tabArray)) {
            $tab->setIsBaseTab($tabArray['isBaseTab']);
        }

        return $tab;
    }
}
