<?php

namespace Cyvian\Src\app\Repositories;

use Illuminate\Support\Facades\DB;

class FieldGroupRepository
{
    public function getFieldGroupIdsByEntryTypeId(int $entryTypeId): array
    {
        return DB::table('entry_type_field_group')
            ->where('entry_type_id', $entryTypeId)
            ->pluck('field_group_id')
            ->toArray();
    }
}
