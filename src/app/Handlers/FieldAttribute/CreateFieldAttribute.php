<?php

namespace Cyvian\Src\app\Handlers\FieldAttribute;

use Cyvian\Src\App\Models\Cyvian\FieldAttribute;
use Cyvian\Src\app\Repositories\FieldAttributeRepository;

class CreateFieldAttribute
{
    private $fieldAttributeRepository;

    public function __construct(FieldAttributeRepository $fieldAttributeRepository)
    {
        $this->fieldAttributeRepository = $fieldAttributeRepository;
    }

    public function handle(string $key, string $value, int $fieldId): FieldAttribute
    {
        return $this->fieldAttributeRepository->createFieldAttribute(
            $key,
            $value,
            $fieldId
        );
    }
}
