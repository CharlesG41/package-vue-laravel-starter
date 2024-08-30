<?php

namespace Cvyian\Src\app\Handlers\Action;

use Cyvian\Src\app\Handlers\Action\BaseActions\CreateCreateAction;
use Cyvian\Src\app\Handlers\Action\BaseActions\CreateDeleteAction;
use Cyvian\Src\app\Handlers\Action\BaseActions\CreateDuplicateAction;
use Cyvian\Src\app\Handlers\Action\BaseActions\CreateEditAction;
use Cyvian\Src\app\Handlers\Action\BaseActions\CreateListAction;
use Cyvian\Src\app\Handlers\Action\BaseActions\CreateMassDeleteAction;
use Cyvian\Src\app\Handlers\Action\BaseActions\CreateModifyFieldsAction;
use Cyvian\Src\app\Handlers\HandlerResponse;
use Cyvian\Src\app\Repositories\ActionEntryTypeRoleRepository;
use Cyvian\Src\app\Repositories\ActionRepository;
use Cyvian\Src\app\Repositories\ActionTranslationRepository;
use Cyvian\Src\app\Repositories\FieldAttributeRepository;
use Cyvian\Src\app\Repositories\FieldRepository;
use Cyvian\Src\app\Repositories\FieldValueRepository;
use Cyvian\Src\app\Repositories\LocaleRepository;

class CreateBaseActions
{
    private $actionRepository;
    private $actionTranslationRepository;
    private $actionEntryTypeRoleRepository;
    private $fieldRepository;
    private $fieldAttributeRepository;
    private $localeRepository;

    public function __construct(
        ActionRepository $actionRepository,
        ActionTranslationRepository $actionTranslationRepository,
        ActionEntryTypeRoleRepository $actionEntryTypeRoleRepository,
        FieldRepository $fieldRepository,
        FieldAttributeRepository $fieldAttributeRepository,
        LocaleRepository $localeRepository
    )
    {
        $this->actionRepository = $actionRepository;
        $this->actionTranslationRepository = $actionTranslationRepository;
        $this->actionEntryTypeRoleRepository = $actionEntryTypeRoleRepository;
        $this->fieldRepository = $fieldRepository;
        $this->fieldAttributeRepository = $fieldAttributeRepository;
        $this->localeRepository = $localeRepository;
    }

    public function handle(
        bool $modifyFields,
        bool $list,
        bool $create,
        bool $edit,
        bool $delete,
        bool $massDelete,
        bool $duplicate,
        int $entryTypeId,
        array $roleIds
    ) : array
    {
        $createModifyFieldsAction = new CreateModifyFieldsAction(
            $this->actionRepository,
            $this->actionTranslationRepository,
            $this->actionEntryTypeRoleRepository,
            $this->fieldRepository,
            $this->fieldAttributeRepository,
            $this->localeRepository
        );
        $createListAction = new CreateListAction(
            $this->actionRepository,
            $this->actionTranslationRepository,
            $this->actionEntryTypeRoleRepository,
            $this->fieldRepository,
            $this->fieldAttributeRepository,
            $this->localeRepository
        );
        $createCreateAction = new CreateCreateAction(
            $this->actionRepository,
            $this->actionTranslationRepository,
            $this->actionEntryTypeRoleRepository,
            $this->fieldRepository,
            $this->fieldAttributeRepository,
            $this->localeRepository
        );
        $createEditAction = new CreateEditAction(
            $this->actionRepository,
            $this->actionTranslationRepository,
            $this->actionEntryTypeRoleRepository,
            $this->fieldRepository,
            $this->fieldAttributeRepository,
            $this->localeRepository
        );
        $createDeleteAction = new CreateDeleteAction(
            $this->actionRepository,
            $this->actionTranslationRepository,
            $this->actionEntryTypeRoleRepository,
            $this->fieldRepository,
            $this->fieldAttributeRepository,
            $this->localeRepository
        );
        $createMassDeleteAction = new CreateMassDeleteAction(
            $this->actionRepository,
            $this->actionTranslationRepository,
            $this->actionEntryTypeRoleRepository,
            $this->fieldRepository,
            $this->fieldAttributeRepository,
            $this->localeRepository
        );
        $createDuplicateAction = new CreateDuplicateAction(
            $this->actionRepository,
            $this->actionTranslationRepository,
            $this->actionEntryTypeRoleRepository,
            $this->fieldRepository,
            $this->fieldAttributeRepository,
            $this->localeRepository
        );

        $actions = [];

        if($modifyFields) {
            $actions[] = $createModifyFieldsAction->handle($entryTypeId, $roleIds);
        }
        if($list) {
            $actions[] = $createListAction->handle($entryTypeId, $roleIds);
        }
        if($create) {
            $actions[] = $createCreateAction->handle($entryTypeId, $roleIds);
        }
        if($edit) {
            $actions[] = $createEditAction->handle($entryTypeId, $roleIds, false);
        }
        if($delete) {
            $actions[] = $createDeleteAction->handle($entryTypeId, $roleIds, false);
        }
        if($massDelete) {
            $actions[] = $createMassDeleteAction->handle($entryTypeId, $roleIds);
        }
        if($duplicate) {
            $actions[] = $createDuplicateAction->handle($entryTypeId, $roleIds, false);
        }

        return $actions;
    }
}
