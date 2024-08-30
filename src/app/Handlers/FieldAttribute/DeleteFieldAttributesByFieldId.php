<?php

namespace Cyvian\Src\app\Handlers\FieldAttribute;

use Cyvian\Src\app\Repositories\FieldAttributeRepository;

class DeleteFieldAttributesByFieldId
{
    private $fieldAttributeRepository;

    public function __construct(FieldAttributeRepository $fieldAttributeRepository)
    {
        $this->fieldAttributeRepository = $fieldAttributeRepository;
    }

    public function handle(int $fieldId)
    {
        $this->fieldAttributeRepository->deleteFieldAttributesByFieldId($fieldId);
    }
}
