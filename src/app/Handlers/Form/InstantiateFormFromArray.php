<?php

namespace Cyvian\Src\app\Handlers\Form;

use Cyvian\Src\app\Classes\Form;
use Cyvian\Src\app\Handlers\Field\InstantiateFieldFromArray;
use Cyvian\Src\app\Handlers\Section\InstantiateSectionFromArray;
use Cyvian\Src\app\Handlers\Tab\InstantiateTabFromArray;

class InstantiateFormFromArray
{
    public function handle(array $sectionsArray): Form
    {
        $instantiateSectionFromArray = new InstantiateSectionFromArray;

        $sections = [];
        foreach ($sectionsArray as $sectionArray) {
            $sections[] = $instantiateSectionFromArray->handle($sectionArray);
        }

        return new Form($sections);
    }
}
