<?php

namespace Cyvian\Src\app\Handlers\Field;

use Cyvian\Src\app\Classes\Form;
use Cyvian\Src\app\Repositories\FieldAttributeRepository;
use Cyvian\Src\app\Repositories\FieldRepository;
use Cyvian\Src\app\Repositories\FieldValueRepository;

class UpdateField
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
        $field->updateFieldInDatabase($this->fieldRepository, $this->fieldAttributeRepository);
        $field->deleteFieldValueInDatabase($this->fieldValueRepository);
        $field->createFieldValueInDatabase($this->fieldValueRepository, $localesByCode, null, $form->entityId);

        return $field;
    }
}
