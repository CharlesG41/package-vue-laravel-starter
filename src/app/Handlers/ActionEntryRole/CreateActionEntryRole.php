<?php
namespace Cyvian\Src\app\Handlers\ActionEntryRole;

use Cyvian\Src\app\Repositories\ActionEntryRoleRepository;
use Cyvian\Src\app\Repositories\RoleRepository;
use Illuminate\Support\Facades\DB;

class CreateActionEntryRole
{
    private $actionEntryRoleRepository;

    public function __construct(ActionEntryRoleRepository $actionEntryRoleRepository)
    {
        $this->actionEntryRoleRepository = $actionEntryRoleRepository;
    }

    public function handle($actionId, int $entryId, array $roleIds)
    {
        foreach ($roleIds as $roleId) {
            $this->actionEntryRoleRepository->createActionEntryRole($actionId, $entryId, $roleId);
        }
    }
}
