<?php

namespace Cyvian\Src\app\Handlers\FlexibleSection;

use Cyvian\Src\app\Handlers\Utils\MergeValueIntoField;
use Cyvian\Src\app\Repositories\FieldValueRepository;
use Cyvian\Src\app\Repositories\FlexibleSectionRepository;

class CreateFlexibleSectionFieldValue
{
    private $fieldValueRepository;

    public function __construct(FieldValueRepository $fieldValueRepository)
    {
        $this->fieldValueRepository = $fieldValueRepository;
    }

    public function handle(array $localesByCode, array $flexibleSections, array $values, int $fieldId, ?int $fieldValueId, ?int $localeId, int $entryId)
    {
        $mergeValueIntoField = new MergeValueIntoField;
        foreach ($values as $sectionValue) {
            $flexibleSection = $this->getFlexibleSectionFromKey($sectionValue['key'], $flexibleSections);
            $parentSectionFieldValue = $this->fieldValueRepository->createFieldValue($fieldId, $fieldValueId, $entryId, $localeId, $flexibleSection->key);
            $fields = $mergeValueIntoField->handle($sectionValue['value'], $flexibleSection->fields);
            foreach ($fields as $field) {
                $field->createFieldValueInDatabase($this->fieldValueRepository, $localesByCode, $parentSectionFieldValue->id, $entryId);
            }
        }
    }

    private function getFlexibleSectionFromKey(string $key, array $flexibleSections)
    {
        foreach ($flexibleSections as $section) {
            if ($section->key === $key) {
                return $section;
            }
        }

        throw new \Exception('Section not found');
    }
}
