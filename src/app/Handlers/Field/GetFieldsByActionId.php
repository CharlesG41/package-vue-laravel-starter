<?php

namespace Cyvian\Src\app\Handlers\Field;

use Cyvian\Src\app\Handlers\Utils\InstantiateFieldFromDatabase;
use Cyvian\Src\App\Models\Cyvian\Action as EloquentAction;
use Cyvian\Src\app\Repositories\FieldRepository;

class GetFieldsByActionId
{
    private $fieldRepository;

    public function __construct(FieldRepository $fieldRepository)
    {
        $this->fieldRepository = $fieldRepository;
    }

    public function handle(int $actionId): array
    {
        $eloquentFields = $this->fieldRepository->getFieldsByEntityIdAndEntityType($actionId, EloquentAction::class, null);

        $instantiateFieldsFromDatabase = new InstantiateFieldFromDatabase;
        $fields = [];

        foreach ($eloquentFields as $eloquentField) {
            $fields[] = $instantiateFieldsFromDatabase->handle($eloquentField);
        }

        return $fields;
    }
}
