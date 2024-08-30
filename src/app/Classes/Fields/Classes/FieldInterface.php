<?php

namespace Cyvian\Src\app\Classes\Fields\Classes;

use Cyvian\Src\App\Models\Cyvian\Field as EloquentField;
use Cyvian\Src\app\Repositories\FieldAttributeRepository;
use Cyvian\Src\app\Repositories\FieldRepository;
use Cyvian\Src\app\Repositories\FieldValueRepository;

interface FieldInterface
{
    static public function instantiateFromEloquentField(EloquentField $eloquentField, ?int $entryId);
    static public function instantiateFromArray(array $data);
    public function createFieldInDatabase(FieldRepository $fieldRepository, FieldAttributeRepository $fieldAttributeRepository);
    public function updateFieldInDatabase(FieldRepository $fieldRepository, FieldAttributeRepository $fieldAttributeRepository);
    public function createFieldValueInDatabase(FieldValueRepository $fieldValueRepository, array $localesByCode, ?int $fieldValueId, int $entryId);
    public function createAttributesInDatabase(FieldAttributeRepository $fieldAttributeRepository);
    public function setValueFromDatabase(array $siteLocales, int $entryId);
    public function getValueForList(string $currentLocaleCode);
    public function getTranslatedValue(string $currentLocaleCode);
    public function isValid($value): bool;
    public function setPosition(string $position);
    public function jsonSerialize();
}
