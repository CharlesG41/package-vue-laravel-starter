<?php

namespace Cyvian\Src\app\Handlers\Field;

use Cyvian\Src\app\Handlers\Utils\GetFieldClassFromType;
use Cyvian\Src\app\Handlers\Utils\SanitizeFieldArrayData;
use Cyvian\Src\App\Utils\Helper;

class InstantiateFieldFromArray
{
    // This function is used to instantiate a field from an array, usually the json that comes from the frontend when an entry is saved
    public function handle(array $data)
    {
        if(!array_key_exists('type', $data)) {
            throw new \Exception('Field type not found');
        }

        $getFieldClassFromType = new GetFieldClassFromType();

        $classToInstantiate = $getFieldClassFromType->handle($data['type']);

        if(!class_exists($classToInstantiate)) {
            throw new \Exception("Class $classToInstantiate not found.");
        }

        $sanitizeFieldArrayData = new SanitizeFieldArrayData();
        $sanitizedData = $sanitizeFieldArrayData->handle($data);

        return $classToInstantiate::instantiateFromArray($sanitizedData);
    }
}
