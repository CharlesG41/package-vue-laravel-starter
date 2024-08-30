<?php

namespace Cyvian\Src\app\Handlers\Action;

use Cyvian\Src\app\Classes\Action;
use Cyvian\Src\app\Handlers\Action\ActionTranslation\UpdateActionTranslation;
use Cyvian\Src\app\Handlers\ActionEntryRole\CreateActionEntryRole;
use Cyvian\Src\app\Handlers\ActionEntryRole\DeleteActionEntryRoleByActionId;
use Cyvian\Src\app\Handlers\ActionEntryTypeRole\CreateActionEntryTypeRole;
use Cyvian\Src\app\Handlers\ActionEntryTypeRole\DeleteActionEntryTypeRoleByActionId;
use Cyvian\Src\app\Handlers\Entry\GetEntriesByEntryType;
use Cyvian\Src\app\Handlers\EntryType\GetEntryTypeByName;
use Cyvian\Src\app\Handlers\HandlerResponse;
use Cyvian\Src\app\Handlers\Role\GetAllRoles;
use Cyvian\Src\app\Repositories\ActionEntryRoleRepository;
use Cyvian\Src\app\Repositories\ActionEntryTypeRoleRepository;
use Cyvian\Src\app\Repositories\ActionRepository;
use Cyvian\Src\app\Repositories\ActionTranslationRepository;
use Cyvian\Src\app\Repositories\EntryRepository;
use Cyvian\Src\app\Repositories\EntryTypeRepository;
use Cyvian\Src\app\Repositories\EntryTypeTranslationRepository;
use Cyvian\Src\app\Repositories\FieldAttributeRepository;
use Cyvian\Src\app\Repositories\FieldRepository;
use Cyvian\Src\app\Repositories\LocaleRepository;
use Cyvian\Src\app\Repositories\RoleRepository;
use Cyvian\Src\app\Repositories\SectionRepository;
use Cyvian\Src\app\Repositories\SectionTranslationRepository;

class UpdateAction
{
    private $actionRepository;
    private $entryRepository;
    private $actionTranslationRepository;
    private $actionEntryRoleRepository;
    private $actionEntryTypeRoleRepository;
    private $fieldRepository;
    private $fieldAttributeRepository;

    public function __construct(
        EntryRepository $entryRepository,
        ActionRepository $actionRepository,
        ActionTranslationRepository $actionTranslationRepository,
        ActionEntryRoleRepository $actionEntryRoleRepository,
        ActionEntryTypeRoleRepository $actionEntryTypeRoleRepository,
        FieldRepository $fieldRepository,
        FieldAttributeRepository $fieldAttributeRepository,
    )
    {
        $this->entryRepository = $entryRepository;
        $this->actionRepository = $actionRepository;
        $this->actionTranslationRepository = $actionTranslationRepository;
        $this->actionEntryRoleRepository = $actionEntryRoleRepository;
        $this->actionEntryTypeRoleRepository = $actionEntryTypeRoleRepository;
        $this->fieldRepository = $fieldRepository;
        $this->fieldAttributeRepository = $fieldAttributeRepository;
    }

    public function handle(Action $action): Action
    {
        $updateActionTranslation = new UpdateActionTranslation(
            $this->actionTranslationRepository
        );
        $deleteActionEntryTypeRoleByActionId = new DeleteActionEntryTypeRoleByActionId(
            $this->actionEntryTypeRoleRepository
        );
        $createActionEntryTypeRole = new CreateActionEntryTypeRole(
            $this->actionEntryTypeRoleRepository
        );
        $deleteActionEntryRoleByActionId = new DeleteActionEntryRoleByActionId(
            $this->actionEntryRoleRepository
        );
        $createActionEntryRole = new CreateActionEntryRole(
            $this->actionEntryRoleRepository
        );

        $oldEloquentAction = $this->actionRepository->getActionById($action->id);
        $this->actionRepository->updateAction($action);

        $updateActionTranslation->handle($action->translation);

        if ($oldEloquentAction->roles_by_entry == $action->rolesByEntry) {
            // it's not changed
            if ($action->rolesByEntry){
                // if its true we need to
                // update the default roles by entry
                // todo
            } else {
                // if its false we need to
                // update action_entry_type_role

                $deleteActionEntryTypeRoleByActionId->handle($action->id);
                $createActionEntryTypeRole->handle($action->id, $action->entryTypeId, $action->roleIds);
            }
        } else {
            // it's changed
            if ($action->rolesByEntry){
                // if its true we need to
                // delete old action_entry_type_role
                $deleteActionEntryTypeRoleByActionId->handle($action->id);
                // create a action_entry_role for every entry corresponding to the default roles
                $oldEloquentEntries = $this->entryRepository->getAllEntriesByEntryTypeId($action->entryTypeId);
                foreach($oldEloquentEntries as $eloquentEntry) {
                    $createActionEntryRole->handle($action->id, $eloquentEntry->id, $action->roleIds);
                }
            } else {
                // if its false we need to
                // delete old action_entry_role
                $deleteActionEntryRoleByActionId->handle($action->id);
                // create a action_entry_type_role with the roles indicated
                $createActionEntryTypeRole->handle($action->id, $action->entryTypeId, $action->roleIds);
            }
        }

        // create or update fields
        foreach ($action->fields as $field) {
            if ($field->id) {
                $field->updateFieldInDatabase($this->fieldRepository, $this->fieldAttributeRepository);
            } else {
                $field->setEntityId($action->id);
                $field->setEntityType(Action::class);
                $field->setIsBaseField(false);
                $field->createFieldInDatabase($this->fieldRepository, $this->fieldAttributeRepository);
            }
        }

        return $action;
    }
}
