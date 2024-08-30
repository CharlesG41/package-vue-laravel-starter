<?php

namespace Cyvian\Src\app\Handlers\Section;

use Cyvian\Src\app\Repositories\FieldRepository;
use Cyvian\Src\app\Repositories\LocaleRepository;
use Cyvian\Src\app\Repositories\SectionRepository;
use Cyvian\Src\app\Repositories\SectionTranslationRepository;

class GetSectionsByEntityIdAndEntityType
{
    private $sectionRepository;
    private $sectionTranslationRepository;
    private $fieldRepository;
    private $localeRepository;

    public function __construct(
        SectionRepository $sectionRepository,
        SectionTranslationRepository $sectionTranslationRepository,
        FieldRepository $fieldRepository,
        LocaleRepository $localeRepository
    )
    {
        $this->sectionRepository = $sectionRepository;
        $this->sectionTranslationRepository = $sectionTranslationRepository;
        $this->fieldRepository = $fieldRepository;
        $this->localeRepository = $localeRepository;
    }

    public function handle(string $entityId, string $entityType, bool $setValues, ?int $entryId): array
    {
        $instantiateSectionFromDatabaseObject = new InstantiateSectionFromDatabaseObject(
            $this->sectionTranslationRepository,
            $this->fieldRepository,
            $this->localeRepository
        );
        $eloquentSections = $this->sectionRepository->getSectionsByEntityIdAndEntityType($entityId, $entityType);

        $sections = [];
        foreach ($eloquentSections as $eloquentSection) {
            $sections[] = $instantiateSectionFromDatabaseObject->handle($eloquentSection, $setValues, $entryId);
        }

        return $sections;
    }
}
