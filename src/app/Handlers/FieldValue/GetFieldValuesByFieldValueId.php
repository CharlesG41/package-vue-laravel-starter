<?php

namespace Cyvian\Src\app\Handlers\FieldValue;

use Cyvian\Src\app\Repositories\FieldValueRepository;
use Illuminate\Database\Eloquent\Collection;

class GetFieldValuesByFieldValueId
{
    private $fieldValueRepository;

    public function __construct(FieldValueRepository $fieldValueRepository)
    {
        $this->fieldValueRepository = $fieldValueRepository;
    }

    public function handle(int $fieldValueId): Collection
    {
        return $this->fieldValueRepository->getFieldValuesByFieldValueId($fieldValueId);
    }
}
