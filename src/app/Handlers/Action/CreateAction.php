<?php

namespace Cyvian\Src\app\Handlers\Action;

use Cyvian\Src\app\Classes\Action;
use Cyvian\Src\app\Classes\Translations\ActionTranslation;
use Cyvian\Src\app\Handlers\Action\ActionTranslation\CreateActionTranslation;
use Cyvian\Src\app\Handlers\ActionEntryTypeRole\CreateActionEntryTypeRole;
use Cyvian\Src\app\Handlers\Field\CreateField;
use Cyvian\Src\app\Handlers\Field\CreateFieldsForAction;
use Cyvian\Src\app\Repositories\ActionEntryTypeRoleRepository;
use Cyvian\Src\app\Repositories\ActionRepository;
use Cyvian\Src\app\Repositories\ActionTranslationRepository;
use Cyvian\Src\app\Repositories\FieldAttributeRepository;
use Cyvian\Src\app\Repositories\FieldRepository;
use Cyvian\Src\app\Repositories\FieldValueRepository;
use Cyvian\Src\app\Repositories\LocaleRepository;

class CreateAction
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

    public function handle(Action $action): Action
    {
        $createActionTranslation = new CreateActionTranslation($this->actionTranslationRepository, $this->localeRepository);
        $createActionEntryTypeRole = new CreateActionEntryTypeRole($this->actionEntryTypeRoleRepository);

        $eloquentAction = $this->actionRepository->createAction($action);
        $action->setId($eloquentAction->id);

        if ($action->translation) {
            $createActionTranslation->handle($action->id, $action->translation);
        }

        if(!$action->rolesByEntry) {
            $createActionEntryTypeRole->handle(
                $action->id,
                $action->entryTypeId,
                $action->roleIds
            );
        }

        foreach ($action->fields as $field) {
            $field->setEntityId($action->id);
            $field->setEntityType(Action::class);
            $field->setIsBaseField(false);
            $field->createFieldInDatabase($this->fieldRepository, $this->fieldAttributeRepository);
        }

        return $action;
    }
}
