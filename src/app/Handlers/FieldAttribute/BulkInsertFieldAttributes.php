<?php

namespace Cyvian\Src\app\Handlers\FieldAttribute;

use Cyvian\Src\app\Handlers\HandlerResponse;
use Cyvian\Src\app\Repositories\FieldAttributeRepository;

class BulkInsertFieldAttributes
{
    private $fieldAttributeRepository;

    public function __construct(FieldAttributeRepository $fieldAttributeRepository)
    {
        $this->fieldAttributeRepository = $fieldAttributeRepository;
    }

    public function handle(array $keyValues, int $fieldId): void
    {
        $fieldAttributesData = [];

        foreach ($keyValues as $key => $value) {
            $fieldAttributesData[] = [
                'key' => $key,
                'value' => $value,
                'field_id' => $fieldId
            ];
        }

        $this->fieldAttributeRepository->bulkInsertFieldAttributes($fieldAttributesData);
    }
}
