<?php

namespace Cyvian\Src\app\Repositories;

use Cyvian\Src\app\Classes\Section;
use Cyvian\Src\App\Models\Cyvian\Section as EloquentSection;
use Illuminate\Database\Eloquent\Collection;

class SectionRepository
{
    public function getSectionsByEntityIdAndEntityType(string $entityId, string $entityType): Collection
    {
        return EloquentSection::where('entity_id', $entityId)
            ->where('entity_type', $entityType)
            ->get();
    }

    public function createSection(Section $section): EloquentSection
    {
        return EloquentSection::create([
            'position' => $section->position,
            'key' => $section->key,
            'entity_id' => $section->entityId,
            'entity_type' => $section->entityType,
        ]);
    }

    public function updateSection(Section $section)
    {
        EloquentSection::where('id', $section->id)->update([
            'position' => $section->position,
            'key' => $section->key,
        ]);
    }

    public function deleteSectionById(int $id)
    {
        EloquentSection::where('id', $id)->delete();
    }

    public function deleteSectionByEntityIdAndEntityType(string $entityId, string $entityType)
    {
        EloquentSection::where('entity_id', $entityId)
            ->where('entity_type', $entityType)
            ->delete();
    }
}
