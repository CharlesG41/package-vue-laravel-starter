<?php

namespace Cyvian\Src\app\Handlers\FieldAttribute;

use Cyvian\Src\app\Repositories\FieldAttributeRepository;

class GetFieldAttributesByFieldId
{
    private $fieldAttributeRepository;

    public function __construct(FieldAttributeRepository $fieldAttributeRepository)
    {
        $this->fieldAttributeRepository = $fieldAttributeRepository;
    }

    public function handle(int $fieldId): array
    {
        $eloquentFieldAttributes =  $this->fieldAttributeRepository->getFieldAttributesByFieldId($fieldId);
        $fieldAttributes = [];
        foreach ($eloquentFieldAttributes as $eloquentFieldAttribute) {
            $fieldAttributes[$eloquentFieldAttribute->key] = $eloquentFieldAttribute->value;
        }

        return $fieldAttributes;
    }
}
