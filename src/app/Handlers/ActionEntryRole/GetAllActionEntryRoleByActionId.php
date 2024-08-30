<?php

namespace Cyvian\Src\app\Handlers\ActionEntryRole;

use Cyvian\Src\app\Repositories\ActionEntryRoleRepository;
use Illuminate\Support\Collection;

class GetAllActionEntryRoleByActionId
{
    private $actionEntryRoleRepository;

    public function __construct(ActionEntryRoleRepository $actionEntryRoleRepository)
    {
        $this->actionEntryRoleRepository = $actionEntryRoleRepository;
    }

    public function handle(int $actionId): Collection
    {
        return $this->actionEntryRoleRepository->getAllActionEntryRoleByActionId($actionId);
    }
}
