<?php
namespace Cyvian\Src\app\Handlers\ActionEntryTypeRole;

use Cyvian\Src\app\Repositories\ActionEntryTypeRoleRepository;
use Illuminate\Support\Facades\DB;

class CreateActionEntryTypeRole
{
    private $actionEntryTypeRoleRepository;

    public function __construct(ActionEntryTypeRoleRepository $actionEntryTypeRoleRepository)
    {
        $this->actionEntryTypeRoleRepository = $actionEntryTypeRoleRepository;
    }

    public function handle($actionId, int $entryTypeId, array $roleIds)
    {
        foreach ($roleIds as $roleId) {
            $this->actionEntryTypeRoleRepository->createActionEntryTypeRole($actionId, $entryTypeId, $roleId);
        }
    }
}
