<?php

namespace Cyvian\Src\app\Handlers\Section\SectionTranslation;

use Cyvian\Src\app\Classes\Translations\SectionTranslation;
use Cyvian\Src\app\Utils\Localisation;

class InstantiateSectionTranslationFromArray
{
    public function handle(array $sectionArray)
    {
        if (!array_key_exists('labels', $sectionArray)) {
            throw new \Exception('Section translation labels not found');
        }

        return new SectionTranslation(
            new Localisation($sectionArray['labels'])
        );
    }
}
