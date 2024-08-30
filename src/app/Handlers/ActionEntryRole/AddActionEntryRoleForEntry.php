<?php

namespace Cyvian\Src\app\Handlers\ActionEntryRole;

use Cyvian\Src\app\Classes\Entry;
use Cyvian\Src\app\Repositories\ActionEntryRoleRepository;

class AddActionEntryRoleForEntry
{
    private $actionEntryRoleRepository;

    public function __construct(ActionEntryRoleRepository $actionEntryRoleRepository)
    {
        $this->actionEntryRoleRepository = $actionEntryRoleRepository;
    }

    // create action_entry_role for the entry, must be called after the rolesIds are merged into the actions
    public function handle(Entry $entry)
    {
        if ($entry->id === null) {
            throw new \Exception('Entry id is not set');
        }
        $createActionEntryRole = new CreateActionEntryRole($this->actionEntryRoleRepository);

        foreach ($entry->actions as $action) {
            if ($action->rolesByEntry) {
                $createActionEntryRole->handle($action->id, $entry->id, $action->roleIds);
            }
        }
    }
}
