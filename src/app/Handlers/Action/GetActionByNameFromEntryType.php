<?php

namespace Cyvian\Src\app\Handlers\Action;

use Cyvian\Src\app\Classes\EntryType;

class GetActionByNameFromEntryType
{
    public function handle(string $actionName, EntryType $entryType)
    {
        foreach($entryType->actions as $action) {
            if ($action->name === $actionName) {
                return $action;
            }
        }

        throw new \Exception('Action not found');
    }
}
