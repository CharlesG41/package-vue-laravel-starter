<?php

namespace Cyvian\Src\app\Handlers\Section;

use Cyvian\Src\app\Classes\Entry;
use Cyvian\Src\app\Classes\EntryType;
use Cyvian\Src\app\Classes\Form;
use Cyvian\Src\app\Classes\Section;
use Cyvian\Src\app\Handlers\Section\SectionTranslation\CreateSectionTranslation;
use Cyvian\Src\app\Repositories\FieldAttributeRepository;
use Cyvian\Src\app\Repositories\FieldRepository;
use Cyvian\Src\app\Repositories\FieldValueRepository;
use Cyvian\Src\app\Repositories\LocaleRepository;
use Cyvian\Src\app\Repositories\SectionRepository;
use Cyvian\Src\app\Repositories\SectionTranslationRepository;

class CreateSection
{
    private $sectionRepository;
    private $sectionTranslationRepository;
    private $fieldRepository;
    private $fieldAttributeRepository;
    private $fieldValueRepository;
    private $localeRepository;

    public function __construct(
        SectionRepository $sectionRepository,
        SectionTranslationRepository $sectionTranslationRepository,
        FieldRepository $fieldRepository,
        FieldAttributeRepository $fieldAttributeRepository,
        FieldValueRepository $fieldValueRepository,
        LocaleRepository $localeRepository
    )
    {
        $this->sectionRepository = $sectionRepository;
        $this->sectionTranslationRepository = $sectionTranslationRepository;
        $this->fieldRepository = $fieldRepository;
        $this->fieldAttributeRepository = $fieldAttributeRepository;
        $this->fieldValueRepository = $fieldValueRepository;
        $this->localeRepository = $localeRepository;
    }

    public function handle(Form $form, Section $section, array $localesByCode)
    {
        $createSectionTranslation = new CreateSectionTranslation($this->sectionTranslationRepository, $this->localeRepository);

        $section->setEntityId($form->entityId);
        $section->setEntityType($form->entityType);
        $section->setIsBaseSection($form->entityType === EntryType::class);
        $eloquentSection = $this->sectionRepository->createSection($section);
        $section->setId($eloquentSection->id);
        $createSectionTranslation->handle($section->translation);

        foreach ($section->fields as $field) {
            if (!$field->id) {
                $field->setEntityId($section->id);
                $field->setEntityType(Section::class);
                $field->setIsBaseField($form->entityType === EntryType::class);
                $field->createFieldInDatabase($this->fieldRepository, $this->fieldAttributeRepository);
            } else {
                $field->updateFieldInDatabase($this->fieldRepository, $this->fieldAttributeRepository);
            }
            if ($form->entityType === Entry::class) {
                $field->setEntryId($form->entityId);
                $field->deleteFieldValueInDatabase($this->fieldValueRepository, $form->entityId);
                $field->createFieldValueInDatabase($this->fieldValueRepository, $localesByCode, null, $form->entityId);
            }
        }

        return $section;
    }
}
