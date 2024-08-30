<?php

namespace Cyvian\Src\app\Handlers\User;

use Cyvian\Src\app\Classes\Action;
use Cyvian\Src\App\Models\User;
use Cyvian\Src\app\Repositories\ActionEntryRoleRepository;
use Cyvian\Src\app\Repositories\ActionEntryTypeRoleRepository;
use Illuminate\Support\Facades\DB;

class CanUserExecuteAction
{
    private $actionEntryRoleRepository;
    private $actionEntryTypeRoleRepository;

    public function __construct(
        ActionEntryRoleRepository $actionEntryRoleRepository,
        ActionEntryTypeRoleRepository $actionEntryTypeRoleRepository
    )
    {
        $this->actionEntryRoleRepository = $actionEntryRoleRepository;
        $this->actionEntryTypeRoleRepository = $actionEntryTypeRoleRepository;
    }

    public function handle(User $user, Action $action, ?int $entryId = null)
    {
        if ($action->rolesByEntry) {
            if ($entryId === null) {
                abort(422, __('cyvian.exceptions.no_entry_found'));
            }

            $results = $this->actionEntryRoleRepository
                ->getActionEntryRoleByActionIdAndEntryIdAndWhereInRoleIds(
                    $action->id,
                    $entryId,
                    $user->roles
                );

            return count($results) > 0;
        } else {
            $results = $this->actionEntryTypeRoleRepository
                ->getActionEntryTypeRoleByActionIdAndEntryIdAndWhereInRoleIds(
                    $action->id,
                    $action->entryTypeId,
                    $user->roles
                );

            return count($results) > 0;
        }
    }
}
