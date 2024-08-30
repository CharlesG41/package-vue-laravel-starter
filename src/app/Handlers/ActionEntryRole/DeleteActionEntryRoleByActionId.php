<?php

namespace Cyvian\Src\app\Handlers\ActionEntryRole;

use Cyvian\Src\app\Repositories\ActionEntryRoleRepository;

class DeleteActionEntryRoleByActionId
{
    private $actionEntryRoleRepository;

    public function __construct(ActionEntryRoleRepository $actionEntryRoleRepository)
    {
        $this->actionEntryRoleRepository = $actionEntryRoleRepository;
    }

    public function handle(int $actionId)
    {
        $this->actionEntryRoleRepository->deleteActionEntryRoleByActionId($actionId);
    }
}
