<?php

namespace Cyvian\Src\app\Handlers\FieldValue;

use Cyvian\Src\app\Repositories\FieldValueRepository;

class DeleteFieldValuesByFieldValueId
{
    private $fieldValueRepository;

    public function __construct(FieldValueRepository $fieldValueRepository)
    {
        $this->fieldValueRepository = $fieldValueRepository;
    }

    public function handle(int $parentFieldValueId)
    {
        $getFieldValuesByFieldValueId = new GetFieldValuesByFieldValueId($this->fieldValueRepository);

        // we need to delete all field values that are related to the field value
        $fieldValuesToBeDeleted = $getFieldValuesByFieldValueId->handle($parentFieldValueId);
        $fieldValueIdsToBeDeleted = $fieldValuesToBeDeleted->pluck('id')->toArray();

        foreach($fieldValueIdsToBeDeleted as $fieldValueId) {
            $this->handle($fieldValueId);
        }

        $this->fieldValueRepository->deleteFieldValuesByFieldValueId($parentFieldValueId);
    }
}
