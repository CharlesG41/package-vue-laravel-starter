<?php

namespace Cyvian\Src\app\Handlers\EntryType;

use Cyvian\Src\app\Classes\EntryType;
use Cyvian\Src\app\Handlers\Action\CreateAction;
use Cyvian\Src\app\Handlers\EntryType\EntryTypeTranslation\CreateEntryTypeTranslation;
use Cyvian\Src\app\Handlers\Form\SaveForm;
use Cyvian\Src\app\Handlers\HandlerResponse;
use Cyvian\Src\app\Handlers\Manager\CreateManagerFile;
use Cyvian\Src\app\Handlers\Manager\CreateModelFile;
use Cyvian\Src\app\Repositories\ActionEntryTypeRoleRepository;
use Cyvian\Src\app\Repositories\ActionRepository;
use Cyvian\Src\app\Repositories\ActionTranslationRepository;
use Cyvian\Src\app\Repositories\EntryTypeRepository;
use Cyvian\Src\app\Repositories\EntryTypeTranslationRepository;
use Cyvian\Src\app\Repositories\FieldAttributeRepository;
use Cyvian\Src\app\Repositories\FieldRepository;
use Cyvian\Src\app\Repositories\FieldValueRepository;
use Cyvian\Src\app\Repositories\LocaleRepository;
use Cyvian\Src\app\Repositories\SectionRepository;
use Cyvian\Src\app\Repositories\SectionTranslationRepository;
use Illuminate\Support\Facades\DB;

class CreateEntryType
{
    private $entryTypeRepository;
    private $entryTypeTranslationRepository;
    private $actionRepository;
    private $actionTranslationRepository;
    private $actionEntryTypeRoleRepository;
    private $fieldRepository;
    private $fieldAttributeRepository;
    private $sectionRepository;
    private $sectionTranslationRepository;
    private $localeRepository;
    private $fieldValueRepository;

    public function __construct(
        EntryTypeRepository $entryTypeRepository,
        EntryTypeTranslationRepository $entryTypeTranslationRepository,
        ActionRepository $actionRepository,
        ActionTranslationRepository $actionTranslationRepository,
        ActionEntryTypeRoleRepository $actionEntryTypeRoleRepository,
        FieldRepository $fieldRepository,
        FieldAttributeRepository $fieldAttributeRepository,
        FieldValueRepository $fieldValueRepository,
        SectionRepository $sectionRepository,
        SectionTranslationRepository $sectionTranslationRepository,
        LocaleRepository $localeRepository
    )
    {
        $this->entryTypeRepository = $entryTypeRepository;
        $this->actionRepository = $actionRepository;
        $this->entryTypeTranslationRepository = $entryTypeTranslationRepository;
        $this->actionTranslationRepository = $actionTranslationRepository;
        $this->actionEntryTypeRoleRepository = $actionEntryTypeRoleRepository;
        $this->fieldRepository = $fieldRepository;
        $this->fieldAttributeRepository = $fieldAttributeRepository;
        $this->fieldValueRepository = $fieldValueRepository;
        $this->sectionRepository = $sectionRepository;
        $this->sectionTranslationRepository = $sectionTranslationRepository;
        $this->localeRepository = $localeRepository;
    }

    public function handle(EntryType $entryType, bool $withStub = false): EntryType
    {
        $createEntryTypeTranslation = new CreateEntryTypeTranslation($this->entryTypeTranslationRepository);
        $createAction = new CreateAction(
            $this->actionRepository,
            $this->actionTranslationRepository,
            $this->actionEntryTypeRoleRepository,
            $this->fieldRepository,
            $this->fieldAttributeRepository,
            $this->localeRepository
        );
        $saveForm = new SaveForm(
            $this->fieldRepository,
            $this->fieldAttributeRepository,
            $this->fieldValueRepository,
            $this->sectionRepository,
            $this->sectionTranslationRepository,
            $this->localeRepository
        );
        $createManagerFile = new CreateManagerFile;
        $createModelFile = new CreateModelFile;

        DB::beginTransaction();

        try {
            $eloquentEntryType = $this->entryTypeRepository->createEntryType($entryType->name, $entryType->type, $entryType->menuSectionId);
            $entryType->setId($eloquentEntryType->id);
            $entryType->form->setEntityId($entryType->id);
            $entryType->form->setEntityType(EntryType::class);
            foreach ($entryType->actions as $action) {
                $action->setEntryTypeId($entryType->id);
            }

            $createEntryTypeTranslation->handle($entryType->translation->singularNames, $entryType->translation->pluralNames, $entryType->id);

            $saveForm->handle($entryType->form);

            foreach($entryType->actions as $action) {
                $createAction->handle($action);
            }
            if ($withStub) {
                $createManagerFile->handle($entryType->name, $entryType->type);
                $createModelFile->handle($entryType->name);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }

        DB::commit();

        return $entryType;
    }
}
