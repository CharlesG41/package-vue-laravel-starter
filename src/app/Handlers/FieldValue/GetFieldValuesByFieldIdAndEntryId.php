<?php

namespace Cyvian\Src\app\Handlers\FieldValue;

use Cyvian\Src\app\Repositories\FieldValueRepository;
use Illuminate\Database\Eloquent\Collection;

class GetFieldValuesByFieldIdAndEntryId
{
    private $fieldValueRepository;

    public function __construct(FieldValueRepository  $fieldValueRepository)
    {
        $this->fieldValueRepository = $fieldValueRepository;
    }

    public function handle(int $fieldId, int $entryId): Collection
    {
        return $this->fieldValueRepository->getFieldValuesByFieldIdAndEntryId($fieldId, $entryId);
    }
}
