<?php

namespace Cyvian\Src\app\Handlers\Section;

use Cyvian\Src\app\Classes\Section;
use Cyvian\Src\app\Classes\Translations\SectionTranslation;
use Cyvian\Src\app\Handlers\Field\InstantiateFieldFromArray;
use Cyvian\Src\app\Handlers\Section\SectionTranslation\InstantiateSectionTranslationFromArray;
use Cyvian\Src\app\Utils\Localisation;

class InstantiateSectionFromArray
{
    public function handle(array $sectionArray): Section
    {
        $instantiateFieldFromArray = new InstantiateFieldFromArray();
        $instantiateSectionTranslationFromArray = new InstantiateSectionTranslationFromArray();

        $fields = [];

        foreach ($sectionArray['fields'] as $field) {
            $fields[] = $instantiateFieldFromArray->handle($field);
        }

        $sectionTranslation = $instantiateSectionTranslationFromArray->handle($sectionArray);

        $section = new Section(
            $sectionTranslation,
            $sectionArray['key'] ?? null,
            $sectionArray['position'],
            $fields
        );

        if (array_key_exists('id', $sectionArray) && $sectionArray['id'] !== null) {
            $section->setId($sectionArray['id']);
        }

        if (array_key_exists('entityId', $sectionArray) && $sectionArray['entityId'] !== null) {
            $section->setEntityId($sectionArray['entityId']);
        }

        if (array_key_exists('entityType', $sectionArray) && $sectionArray['entityType'] !== null) {
            $section->setEntityType($sectionArray['entityType']);
        }

        if (array_key_exists('isBaseSection', $sectionArray) && $sectionArray['isBaseSection'] !== null) {
            $section->setIsBaseSection($sectionArray['isBaseSection']);
        }

        return $section;
    }
}
