<?php

namespace Cyvian\Src\app\Repositories;

use Cyvian\Src\app\Classes\EntryType;
use Cyvian\Src\App\Models\Cyvian\EntryType as EloquentEntryType;
use Illuminate\Support\Facades\DB;

class EntryTypeRepository
{
    public function getEntryTypes()
    {
        return EloquentEntryType::all();
    }

    public function getEntryTypeByName(string $name): ?EloquentEntryType
    {
        return EloquentEntryType::where('name', $name)->first();
    }

    public function getEntryTypeById(int $id): ?EloquentEntryType
    {
        return EloquentEntryType::find($id);
    }

    public function getEntryTypesByMenuSectionId(int $menuSectionId)
    {
        return EloquentEntryType::where('menu_section_id', $menuSectionId)->get();
    }

    public function getEntryTypeIdByEntryId(int $entryId): int
    {
        return DB::table('entry_types')
            ->join('entries', 'entry_types.id', '=', 'entries.entry_type_id')
            ->where('entries.id', $entryId)
            ->select('entry_types.id')
            ->get()
            ->first()
            ->id;
    }


    public function createEntryType(string $name, string $type, int $menuSectionId): EloquentEntryType
    {
        return EloquentEntryType::create([
            'name' => $name,
            'type' => $type,
            'menu_section_id' => $menuSectionId
        ]);
    }

    public function updateEntryType(EntryType $entryType)
    {
        return EloquentEntryType::where('id', $entryType->id)
            ->update([
                'name' => $entryType->name,
                'type' => $entryType->type,
                'menu_section_id' => $entryType->menuSectionId
            ]);
    }
}
