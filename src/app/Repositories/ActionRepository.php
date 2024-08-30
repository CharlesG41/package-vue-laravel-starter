<?php

namespace Cyvian\Src\app\Repositories;

use Cyvian\Src\App\Models\Cyvian\Action as EloquentAction;
use Cyvian\Src\App\Classes\Action;
use Illuminate\Support\Facades\DB;

class ActionRepository
{
    public function getActionById( int $id): EloquentAction
    {
        return EloquentAction::find($id);
    }

    public function getActionsByPositionAndEntryTypeId(string $position, int $entryTypeId)
    {
        return EloquentAction::where('entry_type_id', $entryTypeId)->where('position', $position)->get();
    }

    public function getActionsEntryTypeId(int $entryTypeId)
    {
        return EloquentAction::where('entry_type_id', $entryTypeId)->get();
    }

    public function getActionsByMenuSectionIdIdAndName(int $menuSectionId, string $name)
    {
        return EloquentAction::whereHas('entryType.menuSection', function ($query) use ($menuSectionId) {
            $query->where('id', $menuSectionId);
        })
            ->where('name', $name)
            ->get();
//        return DB::table('actions')
//            ->select('actions.*')
//            ->join('entry_types', 'actions.entry_type_id', '=', 'entry_types.id')
//            ->join('menu_sections', 'menu_sections.id', '=', 'entry_types.menu_section_id')
//            ->where('menu_sections.id', $menuSectionId)
//            ->where('actions.name', $name)
//            ->get();
    }

    public function createAction(Action $action): Action
    {
        $eloquentAction = EloquentAction::create([
            'name' => $action->name,
            'position' => $action->position,
            'action_type' => $action->actionType,
            'url' => $action->url,
            'entry_type_id' => $action->entryTypeId,
            'roles_by_entry' => $action->rolesByEntry,
        ]);
        $action->setId($eloquentAction->id);

        return $action;
    }

    public function updateAction(Action $action): void
    {
        EloquentAction::where('id', $action->id)->update([
            'name' => $action->name,
            'position' => $action->position,
            'action_type' => $action->actionType,
            'url' => $action->url,
            'roles_by_entry' => $action->rolesByEntry,
        ]);
    }

    public function deleteAction(int $actionId): void
    {
        EloquentAction::destroy($actionId);
    }

    public function deleteActionsByEntryTypeId(int $entryTypeId): void
    {
        EloquentAction::where('entry_type_id', $entryTypeId)->delete();
    }
}
