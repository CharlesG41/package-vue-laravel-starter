<?php

namespace Cyvian\Src\app\Handlers\FieldAttribute;

use Cyvian\Src\app\Repositories\FieldAttributeRepository;

class GetTypeAttributeByFieldId
{
    private $fieldAttributeRepository;

    public function __construct(FieldAttributeRepository $fieldAttributeRepository)
    {
        $this->fieldAttributeRepository = $fieldAttributeRepository;
    }

    public function handle(int $fieldId): string
    {
        return $this->fieldAttributeRepository->getTypeAttributeFromFieldId($fieldId);
    }
}
