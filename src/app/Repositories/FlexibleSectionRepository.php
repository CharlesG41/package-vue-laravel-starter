<?php

namespace Cyvian\Src\app\Repositories;

use Cyvian\Src\app\Classes\Fields\Classes\FlexibleSection;
use Cyvian\Src\app\Models\Cyvian\FlexibleSection as EloquentFlexibleSection;

class FlexibleSectionRepository
{

    public function getFlexibleSectionsByFieldId(int $fieldId)
    {
        return EloquentFlexibleSection::where('field_id', $fieldId)->get();
    }

    public function createFlexibleSection(FlexibleSection $section)
    {
        return EloquentFlexibleSection::create([
            'key' => $section->key,
            'labels' => json_encode($section->labels),
            'field_id' => $section->fieldId,
        ]);
    }
}
