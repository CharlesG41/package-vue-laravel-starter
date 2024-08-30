<?php

namespace Cyvian\Src\app\Handlers\FlexibleSection;

use Cyvian\Src\app\Classes\Fields\Classes\FlexibleSection;
use Cyvian\Src\app\Handlers\Field\GetFieldsByEntityIdAndEntityType;
use Cyvian\Src\app\Models\Cyvian\FlexibleSection as EloquentFlexibleSection;
use Cyvian\Src\app\Repositories\FieldRepository;
use Cyvian\Src\app\Utils\Localisation;

class InstantiateFlexibleSectionFromDatabaseObject
{
    private $fieldRepository;

    public function __construct(FieldRepository $fieldRepository)
    {
        $this->fieldRepository = $fieldRepository;
    }

    public function handle(EloquentFlexibleSection $eloquentFlexibleSection, ?int $entryId): FlexibleSection
    {
        $getFieldsByEntityIdAndEntityType = new GetFieldsByEntityIdAndEntityType($this->fieldRepository);
        $fields = $getFieldsByEntityIdAndEntityType->handle($eloquentFlexibleSection->id, FlexibleSection::class, false, $entryId);

        $flexibleSection = new FlexibleSection(
            $eloquentFlexibleSection->key,
            new Localisation(json_decode($eloquentFlexibleSection->labels, true)),
            $fields
        );
        $flexibleSection->setId($eloquentFlexibleSection->id);
        $flexibleSection->setFieldId($eloquentFlexibleSection->field_id);

        return $flexibleSection;
    }
}
