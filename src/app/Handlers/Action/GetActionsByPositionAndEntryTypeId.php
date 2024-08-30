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

class GetActionsByPositionAndEntryTypeId
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

    public function handle(string $position, int $entryTypeId): array
    {
        if ($position != Action::POSITION_TOP && $position != Action::POSITION_ROW && $position != Action::POSITION_GENERAL) {
            throw new \Exception('Invalid position');
        }

        $instantiateActionFromDatabaseObject = new InstantiateActionFromDatabaseObject($this->actionTranslationRepository, $this->localeRepository, $this->actionEntryTypeRoleRepository, $this->fieldRepository);

        $actions = [];
        $eloquentActions = $this->actionRepository->getActionsByPositionAndEntryTypeId($position, $entryTypeId);

        foreach ($eloquentActions as $eloquentAction) {
            $actions[] = $instantiateActionFromDatabaseObject->handle($eloquentAction);
        }

        return $actions;
    }
}
