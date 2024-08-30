<?php

namespace Cyvian\Src\app\Repositories;

use Cyvian\Src\app\Classes\Entry;
use \Cyvian\Src\App\Models\Cyvian\Entry as EloquentEntry;

class EntryRepository
{
    public function getEntryById(int $entryId)
    {
        return EloquentEntry::find($entryId);
    }

    public function getAllEntriesByEntryTypeId(int $entryTypeId)
    {
        return EloquentEntry::where('entry_type_id', $entryTypeId)->get();
    }

    public function getEntryTypeIdByEntryId(int $entryId)
    {
        return EloquentEntry::find($entryId)->entry_type_id;
    }

    public function createEntry(Entry $entry)
    {
        return EloquentEntry::create([
            'order' => $entry->order,
            'entry_type_id' => $entry->entryTypeId,
            'created_by' => $entry->createdBy,
            'updated_by' => $entry->updatedBy,
        ]);
    }

    public function updateEntry(Entry $entry)
    {
        return EloquentEntry::where('id', $entry->id)->update([
            'order' => $entry->order,
            'created_by' => $entry->createdBy,
            'updated_by' => $entry->updatedBy,
        ]);
    }

    public function deleteEntryById(int $entryId)
    {
        return EloquentEntry::where('id', $entryId)->delete();
    }
}
