<?php

namespace Cyvian\Src\app\Handlers\Field;

use Cyvian\Src\app\Classes\Fields\Classes\BaseField;
use Cyvian\Src\app\Classes\Locale;
use Cyvian\Src\App\Handlers\Locale\GetLocalesByType;
use Cyvian\Src\app\Handlers\Utils\GetFieldClassFromType;
use Cyvian\Src\app\Repositories\FieldRepository;
use Cyvian\Src\app\Repositories\LocaleRepository;
use Cyvian\Src\App\Utils\Helper;

class GetFieldsByEntityIdAndEntityType
{
    private $fieldRepository;

    public function __construct(FieldRepository $fieldRepository)
    {
        $this->fieldRepository = $fieldRepository;
    }

    public function handle(int $entityId, string $entityType, bool $setValues = false, ?int $entryId = null): array
    {
        $getLocalesByType = new GetLocalesByType(new LocaleRepository);
        $instantiateFieldFromDatabaseObject = new InstantiateFieldFromDatabaseObject();

        $siteLocales = $getLocalesByType->handle(Locale::IS_SITE);
        $eloquentFields = $this->fieldRepository->getFieldsByEntityIdAndEntityType($entityId, $entityType, $entryId);

        $fields = [];
        foreach ($eloquentFields as $eloquentField) {
            $fields[] = $instantiateFieldFromDatabaseObject->handle($eloquentField, $siteLocales, $setValues, $entryId);
        }

        return $fields;
    }
}
