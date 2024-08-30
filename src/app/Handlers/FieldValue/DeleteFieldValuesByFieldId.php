<?php

namespace Cyvian\Src\app\Handlers\FieldValue;

use Cyvian\Src\app\Repositories\FieldValueRepository;

class DeleteFieldValuesByFieldId
{
    private $fieldValueRepository;

    public function __construct(FieldValueRepository $fieldValueRepository)
    {
        $this->fieldValueRepository = $fieldValueRepository;
    }

    public function handle(int $fieldId)
    {
        $getFieldValuesByFieldId = new GetFieldValuesByFieldId($this->fieldValueRepository);
        $deleteFieldValuesByFieldValueId = new DeleteFieldValuesByFieldValueId($this->fieldValueRepository);

        // we need to delete all field values that are related to the field
        $fieldValuesToBeDeleted = $getFieldValuesByFieldId->handle($fieldId);
        $fieldValueIdsToBeDeleted = $fieldValuesToBeDeleted->pluck('id')->toArray();

        foreach($fieldValueIdsToBeDeleted as $fieldValueId) {
            $deleteFieldValuesByFieldValueId->handle($fieldValueId);
        }

        $this->fieldValueRepository->deleteFieldValuesByFieldId($fieldId);
    }
}
