<?php

namespace Cyvian\Src\app\Handlers\FlexibleSection;

use Cyvian\Src\app\Classes\Fields\Classes\FlexibleSection;
use Cyvian\Src\app\Handlers\Field\InstantiateFieldFromArray;
use Cyvian\Src\app\Utils\Localisation;

class InstantiateFlexibleSectionFromArray
{
    public function handle(array $flexibleSectionArray)
    {
        $instantiateFieldFromArray = new InstantiateFieldFromArray;

        if (!array_key_exists('labels', $flexibleSectionArray)) {
            throw new \InvalidArgumentException('Flexible section must have labels');
        }

        if (!array_key_exists('fields', $flexibleSectionArray)) {
            throw new \InvalidArgumentException('Flexible section must have fields');
        }

        $fields = [];
        foreach ($flexibleSectionArray['fields'] as $field) {
            $fields[] = $instantiateFieldFromArray->handle($field);
        }

        $flexibleSection = new FlexibleSection(
            $flexibleSectionArray['key'],
            new Localisation($flexibleSectionArray['labels']),
            $fields
        );

        if (array_key_exists('id', $flexibleSectionArray)) {
            $flexibleSection->setId($flexibleSectionArray['id']);
        }

        return $flexibleSection;
    }
}
