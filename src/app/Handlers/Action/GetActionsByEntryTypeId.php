<?php

namespace Cyvian\Src\app\Handlers\Action;

use Cyvian\Src\app\Classes\Action;
use Cyvian\Src\app\Handlers\Action\ActionTranslation\GetActionTranslationsByActionId;
use Cyvian\Src\app\Handlers\ActionEntryTypeRole\GetActionEntryTypeRolesByActionId;
use Cyvian\Src\app\Handlers\Field\GetFieldsByActionId;
use Cyvian\Src\app\Repositories\ActionEntryTypeRoleRepository;
use Cyvian\Src\app\Repositories\ActionRepository;
use Cyvian\Src\app\Repositories\ActionTranslationRepository;
use Cyvian\Src\app\Repositories\FieldRepository;
use Cyvian\Src\app\Repositories\LocaleRepository;

class GetActionsByEntryTypeId
{
    private $actionRepository;
    private $actionTranslationRepository;
    private $actionEntryTypeRoleRepository;
    private $fieldRepository;
    private $localeRepository;

    public function __construct(
        ActionRepository $actionRepository,
        ActionTranslationRepository $actionTranslationRepository,
        ActionEntryTypeRoleRepository $actionEntryTypeRoleRepository,
        FieldRepository $fieldRepository,
        LocaleRepository $localeRepository
    )
    {
        $this->actionRepository = $actionRepository;
        $this->actionTranslationRepository = $actionTranslationRepository;
        $this->actionEntryTypeRoleRepository = $actionEntryTypeRoleRepository;
        $this->fieldRepository = $fieldRepository;
        $this->localeRepository = $localeRepository;
    }

    public function handle(int $entryTypeId): array
    {
        $instanceActionFromDatabaseObject = new InstantiateActionFromDatabaseObject(
            $this->actionTranslationRepository,
            $this->localeRepository,
            $this->actionEntryTypeRoleRepository,
            $this->fieldRepository
        );

        $eloquentActions = $this->actionRepository->getActionsEntryTypeId($entryTypeId);

        $actions = [];
        foreach ($eloquentActions as $eloquentAction) {
            $action = $instanceActionFromDatabaseObject->handle($eloquentAction);
            $actions[] = $action;
        }

        return $actions;
    }
}
