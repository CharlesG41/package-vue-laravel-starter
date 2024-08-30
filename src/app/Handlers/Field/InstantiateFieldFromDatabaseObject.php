<?php

namespace Cyvian\Src\app\Handlers\Field;

use Cyvian\Src\app\Handlers\Utils\GetFieldClassFromType;
use Cyvian\Src\App\Models\Cyvian\Field as EloquentField;

class InstantiateFieldFromDatabaseObject
{
    public function handle(EloquentField $eloquentField, array $siteLocales, bool $setValues = true, int $entryId = null)
    {
        $getFieldClassFromType = new GetFieldClassFromType;

        $fieldClass = $getFieldClassFromType->handle($eloquentField->type);
        $field = $fieldClass::instantiateFromEloquentField($eloquentField, $entryId);

        if ($setValues) {
            if ($entryId === null) {
                throw new \Exception('Entry id is required to get field values');
            }
            $field->setValueFromDatabase($siteLocales, $entryId);
        }

        return $field;
    }
}
