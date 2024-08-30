<?php

namespace Cyvian\Src\app\Handlers\ActionEntryTypeRole;

use Cyvian\Src\app\Repositories\ActionEntryTypeRoleRepository;

class GetActionEntryTypeRolesByActionId
{
    private $actionEntryTypeRoleRepository;

    public function __construct(ActionEntryTypeRoleRepository $actionEntryTypeRoleRepository)
    {
        $this->actionEntryTypeRoleRepository = $actionEntryTypeRoleRepository;
    }

    public function handle(int $actionId)
    {
        return $this->actionEntryTypeRoleRepository->getActionEntryTypeRolesByActionId($actionId);
    }
}
