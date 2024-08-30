<?php

namespace Cyvian\Src\app\Handlers\FlexibleSection;

use Cyvian\Src\app\Classes\Fields\Classes\FlexibleSection;
use Cyvian\Src\app\Repositories\FieldRepository;
use Cyvian\Src\app\Repositories\FlexibleSectionRepository;
use Cyvian\Src\app\Utils\Localisation;

class GetFlexibleSectionsByFieldId
{
    private $flexibleSectionRepository;
    private $fieldRepository;

    public function __construct(
        FlexibleSectionRepository $flexibleSectionRepository,
        FieldRepository $fieldRepository
    )
    {
        $this->flexibleSectionRepository = $flexibleSectionRepository;
        $this->fieldRepository = $fieldRepository;
    }

    public function handle(int $fieldId, ?int $entryId)
    {
        $instantiateFlexibleSectionFromDatabaseObject = new InstantiateFlexibleSectionFromDatabaseObject($this->fieldRepository);

        $eloquentFlexibleSections = $this->flexibleSectionRepository->getFlexibleSectionsByFieldId($fieldId);
        $flexibleSections = [];

        foreach ($eloquentFlexibleSections as $eloquentFlexibleSection) {
            $flexibleSections[] = $instantiateFlexibleSectionFromDatabaseObject->handle($eloquentFlexibleSection, $entryId);
        }

        return $flexibleSections;
    }
}
