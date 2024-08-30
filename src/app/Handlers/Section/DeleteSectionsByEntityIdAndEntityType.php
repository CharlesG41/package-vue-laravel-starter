<?php

namespace Cyvian\Src\app\Handlers\Section;

use Cyvian\Src\app\Repositories\FieldAttributeRepository;
use Cyvian\Src\app\Repositories\FieldRepository;
use Cyvian\Src\app\Repositories\FieldValueRepository;
use Cyvian\Src\app\Repositories\SectionRepository;
use Cyvian\Src\app\Repositories\SectionTranslationRepository;

class DeleteSectionsByEntityIdAndEntityType
{
    private $sectionRepository;
    private $sectionTranslationRepository;
    private $fieldRepository;
    private $fieldAttributeRepository;
    private $fieldValueRepository;

    public function __construct(
        SectionRepository $sectionRepository,
        SectionTranslationRepository $sectionTranslationRepository,
        FieldRepository $fieldRepository,
        FieldAttributeRepository $fieldAttributeRepository,
        FieldValueRepository $fieldValueRepository
    )
    {
        $this->sectionRepository = $sectionRepository;
        $this->sectionTranslationRepository = $sectionTranslationRepository;
        $this->fieldRepository = $fieldRepository;
        $this->fieldAttributeRepository = $fieldAttributeRepository;
        $this->fieldValueRepository = $fieldValueRepository;
    }

    public function handle(int $entityId, string $entityType)
    {
        $getSectionIdsByEntityIdAndEntityType = new GetSectionIdsByEntityIdAndEntityType($this->sectionRepository);
        $deleteSectionById = new DeleteSectionById(
            $this->sectionRepository,
            $this->sectionTranslationRepository,
            $this->fieldRepository,
            $this->fieldAttributeRepository,
            $this->fieldValueRepository
        );

        $sectionIds = $getSectionIdsByEntityIdAndEntityType->handle($entityId, $entityType);

        foreach ($sectionIds as $sectionId) {
            $deleteSectionById->handle($sectionId);
        }
    }
}
