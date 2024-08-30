<?php

namespace Cyvian\Src\app\Handlers\Utils;

use Cyvian\Src\app\Handlers\FieldAttribute\GetTypeAttributeByFieldId;
use Cyvian\Src\App\Models\Cyvian\Field as EloquentField;
use Cyvian\Src\app\Repositories\FieldAttributeRepository;
use Cyvian\Src\App\Utils\Helper;

class InstantiateFieldFromDatabase
{
    // This function is used to instantiate a field from the database usually before sending the json to the frontend
    public function handle(EloquentField $field)
    {
        $getTypeAttributeFromFieldId = new GetTypeAttributeByFieldId(new FieldAttributeRepository);
        $type = $getTypeAttributeFromFieldId->handle($field->id);
        $classToInstantiate = Helper::getFieldClass($type);

        return $classToInstantiate::instantiateFromDatabase($field);
    }
}
