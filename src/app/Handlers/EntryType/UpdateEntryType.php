<?php

namespace Cyvian\Src\app\Handlers\EntryType;

use Cyvian\Src\app\Classes\EntryType;
use Cyvian\Src\app\Handlers\Action\CreateAction;
use Cyvian\Src\app\Handlers\Action\DeleteActionsByEntryTypeId;
use Cyvian\Src\app\Handlers\Action\UpdateAction;
use Cyvian\Src\app\Handlers\EntryType\EntryTypeTranslation\UpdateEntryTypeTranslation;
use Cyvian\Src\app\Handlers\Field\DeleteFieldsByEntityIdAndEntityType;
use Cyvian\Src\app\Handlers\FieldValue\ChangeFieldValueByField;
use Cyvian\Src\app\Handlers\Form\GetFieldsFromForm;
use Cyvian\Src\app\Handlers\Form\SaveForm;
use Cyvian\Src\app\Handlers\HandlerResponse;
use Cyvian\Src\app\Repositories\ActionEntryRoleRepository;
use Cyvian\Src\app\Repositories\ActionEntryTypeRoleRepository;
use Cyvian\Src\app\Repositories\ActionRepository;
use Cyvian\Src\app\Repositories\ActionTranslationRepository;
use Cyvian\Src\app\Repositories\EntryRepository;
use Cyvian\Src\app\Repositories\EntryTypeRepository;
use Cyvian\Src\app\Repositories\EntryTypeTranslationRepository;
use Cyvian\Src\app\Repositories\FieldAttributeRepository;
use Cyvian\Src\app\Repositories\FieldRepository;
use Cyvian\Src\app\Repositories\FieldValueRepository;
use Cyvian\Src\app\Repositories\LocaleRepository;
use Cyvian\Src\app\Repositories\SectionRepository;
use Cyvian\Src\app\Repositories\SectionTranslationRepository;

class UpdateEntryType
{
    private $entryRepository;
    private $entryTypeRepository;
    private $entryTypeTranslationRepository;
    private $actionRepository;
    private $fieldRepository;
    private $fieldAttributeRepository;
    private $fieldValueRepository;
    private $localeRepository;
    private $sectionRepository;
    private $sectionTranslationRepository;
    private $actionTranslationRepository;
    private $actionEntryTypeRoleRepository;
    private $actionEntryRoleRepository;

    public function __construct(
        EntryRepository $entryRepository,
        EntryTypeRepository $entryTypeRepository,
        EntryTypeTranslationRepository $entryTypeTranslationRepository,
        ActionRepository $actionRepository,
        ActionTranslationRepository $actionTranslationRepository,
        ActionEntryRoleRepository $actionEntryRoleRepository,
        ActionEntryTypeRoleRepository $actionEntryTypeRoleRepository,
        FieldRepository $fieldRepository,
        FieldAttributeRepository $fieldAttributeRepository,
        FieldValueRepository $fieldValueRepository,
        SectionRepository $sectionRepository,
        SectionTranslationRepository $sectionTranslationRepository,
        LocaleRepository $localeRepository
    )
    {
        $this->entryRepository = $entryRepository;
        $this->entryTypeRepository = $entryTypeRepository;
        $this->actionRepository = $actionRepository;
        $this->actionTranslationRepository = $actionTranslationRepository;
        $this->actionEntryTypeRoleRepository = $actionEntryTypeRoleRepository;
        $this->actionEntryRoleRepository = $actionEntryRoleRepository;
        $this->localeRepository = $localeRepository;
        $this->sectionRepository = $sectionRepository;
        $this->sectionTranslationRepository = $sectionTranslationRepository;
        $this->entryTypeTranslationRepository = $entryTypeTranslationRepository;
        $this->fieldRepository = $fieldRepository;
        $this->fieldAttributeRepository = $fieldAttributeRepository;
        $this->fieldValueRepository = $fieldValueRepository;
    }

    public function handle(EntryType $entryType): EntryType
    {
        if(!$entryType->id) {
            throw new \Exception('Entry type id not found');
        }

        $getEntryType = new GetEntryTypeById(
            $this->entryTypeRepository,
            $this->entryTypeTranslationRepository,
            $this->actionRepository,
            $this->actionTranslationRepository,
            $this->actionEntryTypeRoleRepository,
            $this->fieldRepository,
            $this->localeRepository,
            $this->sectionRepository,
            $this->sectionTranslationRepository,
        );
        $updateEntryTypeTranslation = new UpdateEntryTypeTranslation(
            $this->entryTypeTranslationRepository
        );
        $changeFieldValueByField = new ChangeFieldValueByField($this->fieldValueRepository);
        $saveForm = new SaveForm(
            $this->fieldRepository,
            $this->fieldAttributeRepository,
            $this->fieldValueRepository,
            $this->sectionRepository,
            $this->sectionTranslationRepository,
            $this->localeRepository,
        );
        $updateAction = new UpdateAction(
            $this->entryRepository,
            $this->actionRepository,
            $this->actionTranslationRepository,
            $this->actionEntryRoleRepository,
            $this->actionEntryTypeRoleRepository,
            $this->fieldRepository,
            $this->fieldAttributeRepository,
        );
        $createAction = new CreateAction(
            $this->actionRepository,
            $this->actionTranslationRepository,
            $this->actionEntryTypeRoleRepository,
            $this->fieldRepository,
            $this->fieldAttributeRepository,
            $this->localeRepository,
        );
        $validateEntryTypeName = new ValidateEntryTypeName($this->entryTypeRepository);
        $getFieldsFromForm = new GetFieldsFromForm;

        if(!$validateEntryTypeName->handle($entryType->name, [$entryType->id])) {
            throw new \Exception('Entry type name already exists');
        }

        $oldEntryType = $getEntryType->handle($entryType->id);

        $oldFields = $getFieldsFromForm->handle($oldEntryType->form);
        $newFields = $getFieldsFromForm->handle($entryType->form);
        // check if type has changed, if so change the field values linked to the type
        foreach($oldFields as $oldField) {
            foreach($newFields as $newField) {
                if($oldField->key == $newField->key) {
                    if ($oldField->type != $newField->type) {
                        // ideally trigger modal
                        $changeFieldValueByField->handle($oldField, $newField);
                    }
                }
            }
        }

        $updateEntryTypeTranslation->handle($entryType->translation);

        $this->entryTypeRepository->updateEntryType($entryType);

        // save the new form
        $entryType->form = $saveForm->handle($entryType->form);

        $actions = [];
        foreach ($entryType->actions as $action) {
            if (!$action->id) {
                $actions[] = $createAction->handle($action);
            } else {
                $actions[] = $updateAction->handle($action);
            }
        }
        $entryType->actions = $actions;

        return $entryType;
    }
}
