<?php

namespace Cyvian\Src\app\Handlers\Action\BaseActions;

use Cyvian\Src\app\Classes\Action;
use Cyvian\Src\app\Handlers\Action\CreateAction;
use Cyvian\Src\app\Handlers\HandlerResponse;
use Cyvian\Src\app\Repositories\ActionEntryTypeRoleRepository;
use Cyvian\Src\app\Repositories\ActionRepository;
use Cyvian\Src\app\Repositories\ActionTranslationRepository;
use Cyvian\Src\app\Repositories\FieldAttributeRepository;
use Cyvian\Src\app\Repositories\FieldRepository;
use Cyvian\Src\app\Repositories\FieldValueRepository;
use Cyvian\Src\app\Repositories\LocaleRepository;

class CreateListAction
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

    public function handle(int $entryTypeId, array $roleIds): Action
    {
        $createAction = new CreateAction(
            $this->actionRepository,
            $this->actionTranslationRepository,
            $this->actionEntryTypeRoleRepository,
            $this->fieldRepository,
            $this->fieldAttributeRepository,
            $this->localeRepository
        );

        $action = new Action(
            'list',
            Action::POSITION_GENERAL,
            ACTION::ACTION_TYPE_ADMIN,
            null,
            false,
            null,
            $roleIds,
            []
        );
        $action->setEntryTypeId($entryTypeId);

        return $createAction->handle($action);
    }
}
