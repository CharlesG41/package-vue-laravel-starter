<?php

namespace Cyvian\Src\app\Handlers\Utils;

use Cyvian\Src\app\Classes\Action;
use Cyvian\Src\app\Handlers\User\CanUserExecuteAction;
use Cyvian\Src\App\Models\User;
use Cyvian\Src\app\Repositories\ActionEntryRoleRepository;
use Cyvian\Src\app\Repositories\ActionEntryTypeRoleRepository;

class GetSanitizedActionsForList
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

    public function handle(array $actions, string $entryTypeName, int $entryId = null, User $user = null): array
    {
        $canUserExecuteAction = new CanUserExecuteAction(
            $this->actionEntryRoleRepository,
            $this->actionEntryTypeRoleRepository
        );
        $injectActionUrlWithParams = new InjectActionUrlWithParams();

        if ($user !== null) {
            $actions = array_filter($actions, function($action) use($canUserExecuteAction, $user, $entryId) {
                return $canUserExecuteAction->handle($user, $action, $entryId) && $action->position === Action::POSITION_TOP;
            });
        }

        return array_map(function($action) use ($injectActionUrlWithParams, $entryTypeName, $entryId){
            if ($entryId === null) {
                return $injectActionUrlWithParams->handle($action, $entryTypeName);
            } else {
                return $injectActionUrlWithParams->handle($action, $entryTypeName, $entryId);
            }
        }, $actions);
    }
}
