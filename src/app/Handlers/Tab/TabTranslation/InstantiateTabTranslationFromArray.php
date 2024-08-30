<?php

namespace Cyvian\Src\app\Handlers\Tab\TabTranslation;

use Cyvian\Src\app\Classes\Translations\TabTranslation;
use Cyvian\Src\app\Utils\Localisation;

class InstantiateTabTranslationFromArray
{
    public function handle(array $tabArray): TabTranslation
    {
        if (!array_key_exists('labels', $tabArray)) {
            throw new \Exception('Tab translation labels not found');
        }

        return new TabTranslation(
            new Localisation($tabArray['labels'])
        );
    }
}
