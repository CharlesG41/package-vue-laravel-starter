<?php

namespace Cyvian\Src\app\Handlers\Action;

use Cyvian\Src\app\Handlers\Action\ActionTranslation\DeleteActionTranslation;
use Cyvian\Src\app\Handlers\ActionEntryRole\DeleteActionEntryRoleByActionId;
use Cyvian\Src\app\Handlers\ActionEntryTypeRole\DeleteActionEntryTypeRoleByActionId;
use Cyvian\Src\app\Handlers\Field\DeleteFieldsByEntityIdAndEntityType;
use Cyvian\Src\app\Repositories\ActionEntryRoleRepository;
use Cyvian\Src\app\Repositories\ActionEntryTypeRoleRepository;
use Cyvian\Src\app\Repositories\ActionRepository;
use Cyvian\Src\app\Repositories\ActionTranslationRepository;
use Cyvian\Src\app\Repositories\FieldAttributeRepository;
use Cyvian\Src\app\Repositories\FieldRepository;
use Cyvian\Src\app\Repositories\FieldValueRepository;

class DeleteAction
{
    private $actionRepository;
    private $actionTranslationRepository;
    private $fieldRepository;
    private $actionEntryTypeRoleRepository;
    private $actionEntryRoleRepository;
    private $fieldAttributeRepository;
    private $fieldValueRepository;

    public function __construct(
        ActionRepository $actionRepository,
        ActionTranslationRepository $actionTranslationRepository,
        FieldRepository $fieldRepository,
        ActionEntryTypeRoleRepository $actionEntryTypeRoleRepository,
        ActionEntryRoleRepository $actionEntryRoleRepository,
        FieldAttributeRepository $fieldAttributeRepository,
        FieldValueRepository $fieldValueRepository
    )
    {
        $this->actionRepository = $actionRepository;
        $this->actionTranslationRepository = $actionTranslationRepository;
        $this->fieldRepository = $fieldRepository;
        $this->actionEntryTypeRoleRepository = $actionEntryTypeRoleRepository;
        $this->actionEntryRoleRepository = $actionEntryRoleRepository;
        $this->fieldAttributeRepository = $fieldAttributeRepository;
        $this->fieldValueRepository = $fieldValueRepository;
    }


    public function handle(int $actionId)
    {
        $this->actionRepository->deleteAction($actionId);

        $deleteActionTranslation = new DeleteActionTranslation($this->actionTranslationRepository);
        $deleteActionTranslation->handle($actionId);

        $deleteActionEntryTypeRole = new DeleteActionEntryTypeRoleByActionId($this->actionEntryTypeRoleRepository);
        $deleteActionEntryTypeRole->handle($actionId);

        $deleteActionEntryRole = new DeleteActionEntryRoleByActionId($this->actionEntryRoleRepository);
        $deleteActionEntryRole->handle($actionId);

        $deleteFieldsByActionId = new DeleteFieldsByEntityIdAndEntityType($this->fieldRepository, $this->fieldAttributeRepository, $this->fieldValueRepository);
        $deleteFieldsByActionId->handle($actionId);
    }
}
