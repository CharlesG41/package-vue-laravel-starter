<?php

namespace Cyvian\Src\app\Handlers\Field;

use Cyvian\Src\app\Repositories\FieldRepository;

class GetFieldIdsByEntityIdAndEntityType
{
    private $fieldRepository;

    public function __construct(FieldRepository $fieldRepository)
    {
        $this->fieldRepository = $fieldRepository;
    }

    public function handle(int $entityId, string $entityType): array
    {
        return $this->fieldRepository->getFieldIdsByEntityIdAndEntityType($entityId, $entityType);
    }
}
