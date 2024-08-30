<?php

namespace Cyvian\Src\app\Repositories;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ActionEntryRoleRepository
{
    public function getActionEntryRoleByActionIdAndEntryIdAndWhereInRoleIds(int $actionId, int $entryId, array $roleIds): Collection
    {
        return DB::table('action_entry_role')
            ->where('action_id', $actionId)
            ->where('entry_id', $entryId)
            ->whereIn('role_id', $roleIds)
            ->get();
    }

    public function getActionEntryRoleByActionIdAndRoleId(int $actionId, int $roleId): Collection
    {
        return DB::table('action_entry_role')
            ->where('action_id', $actionId)
            ->where('role_id', $roleId)
            ->get();
    }

    public function getAllActionEntryRoleByActionId(int $actionId): Collection
    {
        return DB::table('action_entry_role')
            ->where('action_id', $actionId)
            ->get();
    }

    public function createActionEntryRole(int $actionId, int $entryId, int $roleId): bool
    {
        return DB::table('action_entry_role')->insert([
            'action_id' => $actionId,
            'entry_id' => $entryId,
            'role_id' => $roleId
        ]);
    }

    public function deleteActionEntryRoleByActionId(int $actionId)
    {
        DB::table('action_entry_role')
            ->where('action_id', $actionId)
            ->delete();
    }

    public function deleteActionEntryRoleByRoleId(int $roleId)
    {
        DB::table('action_entry_role')
            ->where('role_id', $roleId)
            ->delete();
    }

    public function deleteActionEntryRoleByEntryId(int $entryId)
    {
        DB::table('action_entry_role')
            ->where('entry_id', $entryId)
            ->delete();
    }
}
