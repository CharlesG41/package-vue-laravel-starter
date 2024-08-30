<?php

namespace Cyvian\Src\app\Repositories;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ActionEntryTypeRoleRepository
{
    public function getActionEntryTypeRoleByActionIdAndEntryIdAndWhereInRoleIds(int $actionId, int $entryTypeId, array $roleIds): Collection
    {
        return DB::table('action_entry_type_role')
            ->where('action_id', $actionId)
            ->where('entry_type_id', $entryTypeId)
            ->whereIn('role_id', $roleIds)
            ->get();
    }

    public function getActionEntryTypeRolesByActionId(int $actionId): \Illuminate\Support\Collection
    {
        return DB::table('action_entry_type_role')
            ->where('action_id', $actionId)
            ->get();
    }

    public function createActionEntryTypeRole(int $actionId, int $entryTypeId, int $roleId)
    {
        DB::table('action_entry_type_role')->insert([
            'action_id' => $actionId,
            'entry_type_id' => $entryTypeId,
            'role_id' => $roleId
        ]);
    }

    public function deleteActionEntryTypeRoleByActionId(int $actionId)
    {
        DB::table('action_entry_type_role')
            ->where('action_id', $actionId)
            ->delete();
    }

    public function deleteActionEntryTypeRoleByRoleId(int $roleId)
    {
        DB::table('action_entry_type_role')
            ->where('role_id', $roleId)
            ->delete();
    }

    public function deleteActionEntryTypeRoleByEntryId(int $entryTypeId)
    {
        DB::table('action_entry_type_role')
            ->where('entry_type_id', $entryTypeId)
            ->delete();
    }
}
