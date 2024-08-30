<?php

namespace Cyvian\Src\app\Handlers\Utils;

use Cyvian\Src\app\Classes\Action;

class InjectActionUrlWithParams
{
    public function handle(Action $action, string $entryTypeName, int $id = null): Action
    {
        $url = str_replace(['{entry_type}', '{id}', '{name}'], [$entryTypeName, $id, $action->name], $action->url);
        $action->url = $url;

        return $action;
    }
}
