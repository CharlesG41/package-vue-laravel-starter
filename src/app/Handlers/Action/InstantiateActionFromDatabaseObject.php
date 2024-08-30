<?php

namespace Cyvian\Src\app\Handlers\Action;


use Cyvian\Src\app\Classes\Action;
use Cyvian\Src\app\Handlers\Action\ActionTranslation\GetActionTranslationsByActionId;
use Cyvian\Src\app\Handlers\ActionEntryTypeRole\GetActionEntryTypeRolesByActionId;
use Cyvian\Src\app\Handlers\Field\GetFieldsByActionId;
use Cyvian\Src\App\Models\Cyvian\Action as EloquentAction;
use Cyvian\Src\app\Repositories\ActionEntryTypeRoleRepository;
use Cyvian\Src\app\Repositories\ActionTranslationRepository;
use Cyvian\Src\app\Repositories\FieldRepository;
use Cyvian\Src\app\Repositories\LocaleRepository;

class InstantiateActionFromDatabaseObject
{
    private $actionTranslationRepository;
    private $localeRepository;
    private $actionEntryTypeRoleRepository;
    private $fieldRepository;

    public function __construct(
        ActionTranslationRepository $actionTranslationRepository,
        LocaleRepository $localeRepository,
        ActionEntryTypeRoleRepository $actionEntryTypeRoleRepository,
        FieldRepository $fieldRepository
    )
    {
        $this->actionTranslationRepository = $actionTranslationRepository;
        $this->localeRepository = $localeRepository;
        $this->actionEntryTypeRoleRepository = $actionEntryTypeRoleRepository;
        $this->fieldRepository = $fieldRepository;
    }

    public function handle(EloquentAction $eloquentAction, bool $withFields = true): Action
    {
        $getActionTranslationsByActionId = new GetActionTranslationsByActionId($this->actionTranslationRepository, $this->localeRepository);
        $getActionEntryTypeRolesByActionId = new GetActionEntryTypeRolesByActionId($this->actionEntryTypeRoleRepository);
        $getFieldsByActionId = new GetFieldsByActionId($this->fieldRepository);

        $actionTranslation = $getActionTranslationsByActionId->handle($eloquentAction->id);
        $roleIds = $getActionEntryTypeRolesByActionId->handle($eloquentAction->id)->pluck('role_id')->toArray();

        $fields = [];
        if ($withFields) {
            $fields = $getFieldsByActionId->handle($eloquentAction->id);
        }

        $action = new Action(
            $eloquentAction->name,
            $eloquentAction->position,
            $eloquentAction->action_type,
            $eloquentAction->url,
            $eloquentAction->roles_by_entry,
            $actionTranslation,
            $roleIds,
            $fields
        );

        $action->setId($eloquentAction->id);
        $action->setEntryTypeId($eloquentAction->entry_type_id);

        return $action;
    }
}
