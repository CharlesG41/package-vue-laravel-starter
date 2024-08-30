<?php

namespace Cyvian\Src\app\Handlers\Field;

use Cyvian\Src\app\Classes\EntryType;
use Cyvian\Src\app\Classes\Form;
use Cyvian\Src\app\Handlers\FieldValue\DeleteFieldValuesByFieldId;
use Cyvian\Src\app\Repositories\FieldAttributeRepository;
use Cyvian\Src\app\Repositories\FieldRepository;
use Cyvian\Src\app\Repositories\FieldValueRepository;

class CreateOrUpdateField
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

    public function handle(Form $form, $field, array $localesByCode)
    {
        $updateField = new UpdateField(
            $this->fieldRepository,
            $this->fieldAttributeRepository,
            $this->fieldValueRepository
        );

        if (!$field->id) {
            // not true, what if the field is a child of a tab
            $field->setEntityId($form->entityId);
            $field->setEntityType($form->entityType);
            $field->setIsBaseField($form->entityType === EntryType::class);
            $field->createFieldInDatabase($this->fieldRepository, $this->fieldAttributeRepository);
        } else {
            $field->updateFieldInDatabase($this->fieldRepository, $this->fieldAttributeRepository);
        }

        return $field;
    }
}
