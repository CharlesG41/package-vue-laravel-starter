<?php

namespace Cyvian\Src\app\Handlers\FieldValue;

use Cyvian\Src\app\Repositories\FieldValueRepository;

class CreateFieldValue
{
    private $fieldValueRepository;

    public function __construct(FieldValueRepository $fieldValueRepository)
    {
        $this->fieldValueRepository = $fieldValueRepository;
    }

    public function handle(int $fieldId, int $fieldValueId, int $entryId, int $localeId, string $key, string $value)
    {
        $this->fieldValueRepository->createFieldValue($fieldId, $fieldValueId, $entryId, $localeId, $key, $value);
    }
}
