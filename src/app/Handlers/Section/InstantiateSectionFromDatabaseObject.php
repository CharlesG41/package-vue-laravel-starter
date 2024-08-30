<?php

namespace Cyvian\Src\app\Handlers\Section;

use Cyvian\Src\app\Classes\EntryType;
use Cyvian\Src\app\Classes\Section;
use Cyvian\Src\app\Handlers\Field\GetFieldsByEntityIdAndEntityType;
use Cyvian\Src\app\Handlers\Section\SectionTranslation\GetSectionTranslationBySectionId;
use Cyvian\Src\App\Models\Cyvian\Section as EloquentSection;
use Cyvian\Src\app\Repositories\FieldRepository;
use Cyvian\Src\app\Repositories\LocaleRepository;
use Cyvian\Src\app\Repositories\SectionTranslationRepository;

class InstantiateSectionFromDatabaseObject
{
    private $sectionTranslationRepository;
    private $fieldRepository;
    private $localeRepository;

    public function __construct(
        SectionTranslationRepository $sectionTranslationRepository,
        FieldRepository $fieldRepository,
        LocaleRepository $localeRepository
    )
    {
        $this->sectionTranslationRepository = $sectionTranslationRepository;
        $this->fieldRepository = $fieldRepository;
        $this->localeRepository = $localeRepository;
    }

    public function handle(EloquentSection $eloquentSection, bool $setValues, ?int $entryId): Section
    {
        $getSectionTranslationBySectionId = new GetSectionTranslationBySectionId(
            $this->sectionTranslationRepository,
            $this->localeRepository
        );
        $getFieldsByEntityIdAndEntityType = new GetFieldsByEntityIdAndEntityType($this->fieldRepository);

        $sectionTranslation = $getSectionTranslationBySectionId->handle($eloquentSection->id);
        $fields = $getFieldsByEntityIdAndEntityType->handle($eloquentSection->id, Section::class, $setValues, $entryId);

        $section = new Section(
            $sectionTranslation,
            $eloquentSection->key,
            $eloquentSection->position,
            $fields,
        );
        $section->setId($eloquentSection->id);
        $section->setEntityId($eloquentSection->entity_id);
        $section->setEntityType($eloquentSection->entity_type);
        $section->setIsBaseSection($eloquentSection->entity_type === EntryType::class);

        return $section;
    }
}
