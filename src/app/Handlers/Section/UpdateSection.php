<?php

namespace Cyvian\Src\app\Handlers\Section;

use Cyvian\Src\app\Classes\Entry;
use Cyvian\Src\app\Classes\EntryType;
use Cyvian\Src\app\Classes\Form;
use Cyvian\Src\app\Classes\Section;
use Cyvian\Src\app\Handlers\Section\SectionTranslation\CreateSectionTranslation;
use Cyvian\Src\app\Handlers\Section\SectionTranslation\DeleteSectionTranslationBySectionId;
use Cyvian\Src\app\Repositories\FieldAttributeRepository;
use Cyvian\Src\app\Repositories\FieldRepository;
use Cyvian\Src\app\Repositories\FieldValueRepository;
use Cyvian\Src\app\Repositories\LocaleRepository;
use Cyvian\Src\app\Repositories\SectionRepository;
use Cyvian\Src\app\Repositories\SectionTranslationRepository;

class UpdateSection
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

    public function handle(Form $form, Section $section, array $localesByCode): Section
    {
        $deleteSectionTranslationsBySectionId = new DeleteSectionTranslationBySectionId($this->sectionTranslationRepository);
        $createSectionTranslation = new CreateSectionTranslation($this->sectionTranslationRepository, $this->localeRepository);

        if ($section->id === null) {
            throw new \Exception('Section id is required to update section');
        }

        $deleteSectionTranslationsBySectionId->handle($section->id);
        $createSectionTranslation->handle($section->translation);

        $this->sectionRepository->updateSection($section);

        foreach ($section->fields as $field) {
            if (!$field->id) {
                // if the field doesn't have an id, then we create it, otherwise, we update it
                $field->setEntityId($section->id);
                $field->setEntityType(Section::class);
                // if form is an entry form and the field is not a base field, then we need to set the entry id
                if ($form->entityType === Entry::class && !$field->isBaseField) {
                    $field->setEntryId($form->entityId);
                }
                // if the form.entryType is EntryType, then the field will become a base field, otherwise, it's either an action or an entry field so not a base field
                $field->setIsBaseField($form->entityType === EntryType::class);
                $field->createFieldInDatabase($this->fieldRepository, $this->fieldAttributeRepository);
            } else {
                $field->updateFieldInDatabase($this->fieldRepository, $this->fieldAttributeRepository);
            }
            // if the entity is an entry, we need handle the field values
            if ($form->entityType === Entry::class) {
                $field->deleteFieldValueInDatabase($this->fieldValueRepository, $form->entityId);
                $field->createFieldValueInDatabase($this->fieldValueRepository, $localesByCode, null, $form->entityId);
            }
        }

        return $section;
    }
}
