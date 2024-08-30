<?php

namespace Cyvian\Src\app\Handlers\ActionEntryTypeRole;

use Cyvian\Src\app\Repositories\ActionEntryTypeRoleRepository;

class DeleteActionEntryTypeRoleByActionId
{
    private $actionEntryTypeRoleRepository;

    public function __construct(ActionEntryTypeRoleRepository $actionEntryTypeRoleRepository)
    {
        $this->actionEntryTypeRoleRepository = $actionEntryTypeRoleRepository;
    }

    public function handle(int $actionId)
    {
        $this->actionEntryTypeRoleRepository->deleteActionEntryTypeRoleByActionId($actionId);
    }
}
