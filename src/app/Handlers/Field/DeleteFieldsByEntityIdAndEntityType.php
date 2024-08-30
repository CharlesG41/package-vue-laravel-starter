<?php

namespace Cyvian\Src\app\Handlers\Field;

use Cyvian\Src\app\Classes\Fields\Classes\BaseField;
use Cyvian\Src\app\Handlers\FieldAttribute\DeleteFieldAttributesByFieldId;
use Cyvian\Src\app\Handlers\FieldValue\DeleteFieldValuesByFieldId;
use Cyvian\Src\app\Repositories\FieldAttributeRepository;
use Cyvian\Src\app\Repositories\FieldRepository;
use Cyvian\Src\app\Repositories\FieldValueRepository;

class DeleteFieldsByEntityIdAndEntityType
{
    private $fieldRepository;
    private $fieldAttributeRepository;
    private $fieldValueRepository;

    public function __construct(FieldRepository $fieldRepository, FieldAttributeRepository $fieldAttributeRepository, FieldValueRepository $fieldValueRepository)
    {
        $this->fieldRepository = $fieldRepository;
        $this->fieldAttributeRepository = $fieldAttributeRepository;
        $this->fieldValueRepository = $fieldValueRepository;
    }

    public function handle(int $entityId, string $entityType): void
    {
        $getFieldIdsByEntityIdAndEntityType = new GetFieldIdsByEntityIdAndEntityType($this->fieldRepository);
        $deleteFieldById = new DeleteFieldById($this->fieldRepository, $this->fieldAttributeRepository, $this->fieldValueRepository);

        // get field ids that will be deleted
        $fieldIds = $getFieldIdsByEntityIdAndEntityType->handle($entityId, $entityType);

        foreach ($fieldIds as $fieldId) {
           $deleteFieldById->handle($fieldId);
        }
    }
}
