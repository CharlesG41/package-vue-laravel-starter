<?php

namespace Cyvian\Src\app\Handlers\FieldValue;

use Cyvian\Src\app\Repositories\FieldValueRepository;

class GetFieldValuesByFieldIdAndEntryIdAndLocaleId
{
    private $fieldValueRepository;

    public function __construct(FieldValueRepository $fieldValueRepository)
    {
        $this->fieldValueRepository = $fieldValueRepository;
    }

    public function handle(int $fieldId, int $localeId, int $entryId)
    {
        return $this->fieldValueRepository->getFieldValuesByFieldIdAndLocaleIdAndEntryId($fieldId, $localeId, $entryId);
    }
}
