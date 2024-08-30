<?php

namespace Cyvian\Src\app\Handlers\Form;

use Cyvian\Src\app\Classes\EntryType;
use Cyvian\Src\app\Classes\Form;
use Cyvian\Src\app\Classes\Locale;
use Cyvian\Src\app\Handlers\Field\DeleteFieldById;
use Cyvian\Src\app\Handlers\Field\GetFieldIdsByEntityIdAndEntityType;
use Cyvian\Src\app\Handlers\Locale\GetLocalesAsArrayKeyCodeByType;
use Cyvian\Src\app\Handlers\Section\CreateSection;
use Cyvian\Src\app\Handlers\Section\DeleteSectionById;
use Cyvian\Src\app\Handlers\Section\GetSectionIdsByEntityIdAndEntityType;
use Cyvian\Src\app\Handlers\Section\UpdateSection;
use Cyvian\Src\app\Repositories\FieldAttributeRepository;
use Cyvian\Src\app\Repositories\FieldRepository;
use Cyvian\Src\app\Repositories\FieldValueRepository;
use Cyvian\Src\app\Repositories\LocaleRepository;
use Cyvian\Src\app\Repositories\SectionRepository;
use Cyvian\Src\app\Repositories\SectionTranslationRepository;

class SaveForm
{
    private $fieldRepository;
    private $fieldAttributeRepository;
    private $fieldValueRepository;
    private $sectionRepository;
    private $sectionTranslationRepository;
    private $localeRepository;

    public function __construct(
        FieldRepository $fieldRepository,
        FieldAttributeRepository $fieldAttributeRepository,
        FieldValueRepository $fieldValueRepository,
        SectionRepository $sectionRepository,
        SectionTranslationRepository $sectionTranslationRepository,
        LocaleRepository $localeRepository
    )
    {
        $this->fieldRepository = $fieldRepository;
        $this->fieldAttributeRepository = $fieldAttributeRepository;
        $this->fieldValueRepository = $fieldValueRepository;
        $this->sectionRepository = $sectionRepository;
        $this->sectionTranslationRepository = $sectionTranslationRepository;
        $this->localeRepository = $localeRepository;
    }

    public function handle(Form $form): Form
    {
        $createSection = new CreateSection(
            $this->sectionRepository,
            $this->sectionTranslationRepository,
            $this->fieldRepository,
            $this->fieldAttributeRepository,
            $this->fieldValueRepository,
            $this->localeRepository
        );
        $updateSection = new UpdateSection(
            $this->sectionRepository,
            $this->sectionTranslationRepository,
            $this->fieldRepository,
            $this->fieldAttributeRepository,
            $this->fieldValueRepository,
            $this->localeRepository
        );
        $getLocalesAsArrayKeyCodeByType = new GetLocalesAsArrayKeyCodeByType($this->localeRepository);

        $localesByCode = $getLocalesAsArrayKeyCodeByType->handle(Locale::IS_SITE);

        $createdOrUpdatedFieldIds = [];
        $sectionIds = [];
        foreach ($form->sections as $section) {
            if (!$section->id) {
                $section->setEntityId($form->entityId);
                $section->setEntityType($form->entityType);
                $section->setIsBaseSection($form->entityType === EntryType::class);
                $section = $createSection->handle($form, $section, $localesByCode);
            } else {
                $section = $updateSection->handle($form, $section, $localesByCode);
            }
            $sectionFieldIds = array_map(function($field) {
                return $field->id;
            }, $section->fields);
            $createdOrUpdatedFieldIds = array_merge($createdOrUpdatedFieldIds, $sectionFieldIds);
            $sectionIds[] = $section->id;
        }

        // if a field is not in the updatedFieldIds array and its related to the entry, it means it was deleted by the user so we need to deleted it from the database
        $this->deleteFieldsRelatedToTheEntryThatWereNotCreatedOrUpdated($createdOrUpdatedFieldIds, $form->entityId, $form->entityType);
        // same for sections
        $this->deleteSectionsRelatedToThenEntryThatWereNotCreatedOrUpdated($sectionIds, $form->entityId, $form->entityType);

        return $form;
    }

    private function deleteFieldsRelatedToTheEntryThatWereNotCreatedOrUpdated(array $fieldIdsToKeep, int $entityId, string $entityType)
    {
        $getFieldIdsByEntityIdAndEntityType = new GetFieldIdsByEntityIdAndEntityType($this->fieldRepository);
        $deleteFieldById = new DeleteFieldById(
            $this->fieldRepository,
            $this->fieldAttributeRepository,
            $this->fieldValueRepository
        );

        $allFieldIdsRelatedToTheEntry = $getFieldIdsByEntityIdAndEntityType->handle($entityId, $entityType);
        $fieldIdsToDelete = array_diff($allFieldIdsRelatedToTheEntry, $fieldIdsToKeep);

        foreach ($fieldIdsToDelete as $fieldId) {
            $deleteFieldById->handle($fieldId);
        }
    }

    public function deleteSectionsRelatedToThenEntryThatWereNotCreatedOrUpdated(array $sectionIdsToKeep, int $entityId, string $entityType)
    {
        $getSectionIdsByEntityIdAndEntityType = new GetSectionIdsByEntityIdAndEntityType($this->sectionRepository);
        $deleteSectionById = new DeleteSectionById(
            $this->sectionRepository,
            $this->sectionTranslationRepository,
            $this->fieldRepository,
            $this->fieldAttributeRepository,
            $this->fieldValueRepository
        );

        $sectionIdsRelatedToTheEntry = $getSectionIdsByEntityIdAndEntityType->handle($entityId, $entityType);
        $sectionIdsToDelete = array_diff($sectionIdsRelatedToTheEntry, $sectionIdsToKeep);

        foreach ($sectionIdsToDelete as $tabId) {
            $deleteSectionById->handle($tabId);
        }
    }
}
