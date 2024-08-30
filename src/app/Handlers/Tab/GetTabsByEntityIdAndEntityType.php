<?php

namespace Cyvian\Src\app\Handlers\Tab;

use Cyvian\Src\app\Handlers\Section\InstantiateSectionFromDatabaseObject;
use Cyvian\Src\app\Repositories\FieldRepository;
use Cyvian\Src\app\Repositories\LocaleRepository;
use Cyvian\Src\app\Repositories\SectionRepository;
use Cyvian\Src\app\Repositories\SectionTranslationRepository;

class GetTabsByEntityIdAndEntityType
{
    private $sectionRepository;
    private $sectionTranslationRepository;
    private $localeRepository;
    private $fieldRepository;

    public function __construct(
        SectionRepository $sectionRepository,
        SectionTranslationRepository $sectionTranslationRepository,
        LocaleRepository $localeRepository,
        FieldRepository $fieldRepository
    )
    {
        $this->sectionRepository = $sectionRepository;
        $this->sectionTranslationRepository = $sectionTranslationRepository;
        $this->localeRepository = $localeRepository;
        $this->fieldRepository = $fieldRepository;
    }

    public function handle(int $entityId, string $entityType, bool $setValues = false, int $entryId = null)
    {
        $instantiateSectionFromDatabaseObject = new InstantiateSectionFromDatabaseObject(
            $this->sectionTranslationRepository,
            $this->fieldRepository,
            $this->localeRepository
        );
        $eloquentSections = $this->sectionRepository->getSectionsByEntityIdAndEntityType($entityId, $entityType);

        $tabs = [];
        foreach ($eloquentSections as $eloquentSection) {
            $tabs[] = $instantiateSectionFromDatabaseObject->handle($eloquentSection, $setValues, $entryId);
        }

        return $tabs;
    }
}
