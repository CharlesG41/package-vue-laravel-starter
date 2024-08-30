<?php

namespace Cyvian\Src\app\Handlers\Section;

use Cyvian\Src\app\Classes\Section;
use Cyvian\Src\app\Handlers\Field\DeleteFieldById;
use Cyvian\Src\app\Handlers\Field\GetFieldIdsByEntityIdAndEntityType;
use Cyvian\Src\app\Handlers\Section\SectionTranslation\DeleteSectionTranslationBySectionId;
use Cyvian\Src\app\Repositories\FieldAttributeRepository;
use Cyvian\Src\app\Repositories\FieldRepository;
use Cyvian\Src\app\Repositories\FieldValueRepository;
use Cyvian\Src\app\Repositories\SectionRepository;
use Cyvian\Src\app\Repositories\SectionTranslationRepository;

class DeleteSectionById
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

    public function handle(int $sectionId)
    {
        $getFieldIdsByEntityIdAndEntityType = new GetFieldIdsByEntityIdAndEntityType($this->fieldRepository);
        $deleteFieldById = new DeleteFieldById($this->fieldRepository, $this->fieldAttributeRepository, $this->fieldValueRepository);
        $deleteSectionTranslationBySectionId = new DeleteSectionTranslationBySectionId($this->sectionTranslationRepository);

        $deleteSectionTranslationBySectionId->handle($sectionId);
        $fieldIds = $getFieldIdsByEntityIdAndEntityType->handle($sectionId, Section::class);

        foreach ($fieldIds as $fieldId) {
            $deleteFieldById->handle($fieldId);
        }

        $this->sectionRepository->deleteSectionById($sectionId);
    }
}
