<?php

namespace Cyvian\Src\app\Handlers\Field;

use Cyvian\Src\app\Repositories\FieldRepository;

class GetFieldById
{
    private $fieldRepository;

    public function __construct(FieldRepository $fieldRepository)
    {
        $this->fieldRepository = $fieldRepository;
    }

    public function handle(int $fieldId, array $siteLocales, bool $setValues = true, int $entryId = null)
    {
        $instantiateFieldFromDatabaseObject = new InstantiateFieldFromDatabaseObject;
        $eloquentField = $this->fieldRepository->getFieldById($fieldId);

        return $instantiateFieldFromDatabaseObject->handle($eloquentField, $siteLocales, $setValues, $entryId);
    }
}
