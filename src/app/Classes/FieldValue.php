<?php

namespace Cyvian\Src\app\Classes;

class FieldValue
{
    public $key;
    public $value;
    public $fieldId;
    public $fieldValueId;
    public $entryId;
    public $localeId;

    public function __construct(int $key, string $value, int $fieldId, int $fieldValueId, int $entryId, int $localeId)
    {
        $this->key = $key;
        $this->value = $value;
        $this->fieldId = $fieldId;
        $this->fieldValueId = $fieldValueId;
        $this->entryId = $entryId;
        $this->localeId = $localeId;
    }
}
