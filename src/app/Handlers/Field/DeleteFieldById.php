<?php

namespace Cyvian\Src\app\Handlers\Field;

use Cyvian\Src\app\Classes\Fields\Classes\BaseField;
use Cyvian\Src\app\Handlers\FieldAttribute\DeleteFieldAttributesByFieldId;
use Cyvian\Src\app\Handlers\FieldValue\DeleteFieldValuesByFieldId;
use Cyvian\Src\app\Repositories\FieldAttributeRepository;
use Cyvian\Src\app\Repositories\FieldRepository;
use Cyvian\Src\app\Repositories\FieldValueRepository;

class DeleteFieldById
{
    private $fieldRepository;
    private $fieldAttributeRepository;
    private $fieldValueRepository;

    public function __construct(
        FieldRepository $fieldRepository,
        FieldAttributeRepository $fieldAttributeRepository,
        FieldValueRepository $fieldValueRepository
    )
    {
        $this->fieldRepository = $fieldRepository;
        $this->fieldAttributeRepository = $fieldAttributeRepository;
        $this->fieldValueRepository = $fieldValueRepository;
    }

    public function handle(int $fieldId)
    {
        $deleteFieldAttributesByFieldId = new DeleteFieldAttributesByFieldId($this->fieldAttributeRepository);
        $deleteFieldValuesByFieldId = new DeleteFieldValuesByFieldId($this->fieldValueRepository);
        $deleteFieldByEntityIdAndEntityType = new DeleteFieldsByEntityIdAndEntityType($this->fieldRepository, $this->fieldAttributeRepository, $this->fieldValueRepository);

        // delete field_values
        $deleteFieldValuesByFieldId->handle($fieldId);
        // delete field_attributes
        $deleteFieldAttributesByFieldId->handle($fieldId);
        // delete child fields
        $deleteFieldByEntityIdAndEntityType->handle($fieldId, BaseField::class);
        // delete field
        $this->fieldRepository->deleteFieldById($fieldId);
    }
}
