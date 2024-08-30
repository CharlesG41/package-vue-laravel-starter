<?php

namespace Cyvian\Src\app\Classes\Fields;

use Cyvian\Src\app\Classes\Fields\Classes\BaseField;
use Cyvian\Src\app\Classes\Fields\Classes\FieldInterface;
use Cyvian\Src\app\Classes\Fields\Classes\FieldPermissions;
use Cyvian\Src\app\Classes\Fields\Classes\ValidationResponse;
use Cyvian\Src\app\Classes\Fields\Traits\HasFields;
use Cyvian\Src\app\Classes\Locale;
use Cyvian\Src\app\Classes\ValueForList;
use Cyvian\Src\app\Handlers\Field\GetFieldsByEntityIdAndEntityType;
use Cyvian\Src\app\Handlers\Field\InstantiateFieldFromArray;
use Cyvian\Src\app\Handlers\FieldAttribute\BulkInsertFieldAttributes;
use Cyvian\Src\app\Handlers\FieldAttribute\DeleteFieldAttributesByFieldId;
use Cyvian\Src\app\Handlers\FieldAttribute\GetFieldAttributesByFieldId;
use Cyvian\Src\app\Handlers\FieldValue\GetFieldValuesByFieldIdAndEntryId;
use Cyvian\Src\app\Handlers\FieldValue\GetFieldValuesByFieldIdAndEntryIdAndLocaleId;
use Cyvian\Src\App\Handlers\Locale\GetCurrentLocale;
use Cyvian\Src\App\Handlers\Locale\GetLocalesByType;
use Cyvian\Src\app\Handlers\Utils\MergeValueIntoField;
use Cyvian\Src\app\Handlers\Utils\ValidateValueFromField;
use Cyvian\Src\App\Models\Cyvian\Field as EloquentField;
use Cyvian\Src\app\Repositories\FieldAttributeRepository;
use Cyvian\Src\app\Repositories\FieldRepository;
use Cyvian\Src\app\Repositories\FieldValueRepository;
use Cyvian\Src\app\Repositories\LocaleRepository;
use Cyvian\Src\App\Utils\Localisation;

class Repeater extends BaseField implements FieldInterface, \JsonSerializable
{
    const TYPE = 'repeater';

    public $minimum;
    public $maximum;
    public $fields;

    public function __construct(
        string $key,
        Localisation $name,
        Localisation $description,
        bool $translatable,
        ?int $minimum,
        ?int $maximum,
        array $fields,
        array $conditions,
        FieldPermissions $fieldPermissions,
        bool $locked = false
    )
    {
        foreach ($fields as $field) {
            if (!$field instanceof FieldInterface) {
                throw new \InvalidArgumentException('Field must be an implements FieldInterface');
            }
        }
        $this->minimum = $minimum;
        $this->maximum = $maximum;
        $this->fields = $fields;

        $this->setTranslatable($translatable);

        parent::__construct(
            $key,
            self::TYPE,
            $name,
            $description,
            $translatable,
            false,
            false,
            12,
            $locked,
            $conditions,
            $fieldPermissions
        );
    }

    static public function instantiateFromArray(array $data): self
    {
        $instantiateFieldFromArray = new InstantiateFieldFromArray();

        $conditions = parent::instantiateConditionsFromArray($data['conditions']);
        $fieldPermissions = parent::instantiateFieldPermissionsFromArray($data);

        $fields = [];
        foreach ($data['fields'] as $field) {
            $fields[] = $instantiateFieldFromArray->handle($field);
        }

        $field = new self(
            $data['key'],
            new Localisation($data['name']),
            new Localisation($data['description']),
            $data['translatable'],
            $data['minimum'],
            $data['maximum'],
            $fields,
            $conditions,
            $fieldPermissions,
            $data['locked'],
        );

        $field->baseInstantiateFromArray($data);

        return $field;
    }

    static public function instantiateFromEloquentField(EloquentField $eloquentField, ?int $entryId): self
    {
        $getFieldAttributesByFieldId = new GetFieldAttributesByFieldId(new FieldAttributeRepository);
        $getFieldsByEntityIdAndEntityType = new GetFieldsByEntityIdAndEntityType(new FieldRepository);

        $fieldAttributesAsKeyValue = $getFieldAttributesByFieldId->handle($eloquentField->id);

        $fields = $getFieldsByEntityIdAndEntityType->handle($eloquentField->id, BaseField::class, false, $entryId);

        $conditions = parent::instantiateConditionsFromArray(json_decode($eloquentField->conditions, true));
        $fieldPermissions = parent::instantiateFieldPermissionsFromEloquent($eloquentField);
        $field = new self(
            $eloquentField->key,
            new Localisation(json_decode($eloquentField->name, true)),
            new Localisation(json_decode($eloquentField->description, true)),
            $eloquentField->translatable,
            $fieldAttributesAsKeyValue['minimum'],
            $fieldAttributesAsKeyValue['maximum'],
            $fields,
            $conditions,
            $fieldPermissions,
            $eloquentField->locked,
        );

        $field->setId($eloquentField->id);
        $field->setEntityId($eloquentField->entity_id);
        $field->setEntityType($eloquentField->entity_type);
        $field->setIsBaseField($eloquentField->is_base_field);
        $field->setEntryId($entryId);
        $field->setPosition($fieldAttributesAsKeyValue['position'] ?? null);

        return $field;
    }

    // Used in CreateForm handler
    public function createFieldInDatabase(FieldRepository $fieldRepository, FieldAttributeRepository $fieldAttributeRepository)
    {
        parent::baseCreateFieldInDatabase($fieldRepository);
        $this->createAttributesInDatabase($fieldAttributeRepository);

        foreach ($this->fields as $field) {
            $field->setEntityId($this->id);
            $field->setEntityType(BaseField::class);
            $field->createFieldInDatabase($fieldRepository, $fieldAttributeRepository);
        }

        foreach ($this->fields as $field) {
            $field->setEntityId($this->id);
            $field->setEntityType(BaseField::class);
            $field->updateFieldInDatabase($fieldRepository, $fieldAttributeRepository);
        }
    }

    // Used in UpdateForm handler
    public function updateFieldInDatabase(FieldRepository $fieldRepository, FieldAttributeRepository $fieldAttributeRepository)
    {
        parent::baseUpdateFieldInDatabase($fieldRepository);
        $deleteFieldAttributesByFieldId = new DeleteFieldAttributesByFieldId($fieldAttributeRepository);
        $deleteFieldAttributesByFieldId->handle($this->id);
        $this->createAttributesInDatabase($fieldAttributeRepository);
    }

    // Used in CreateForm handler
    public function createFieldValueInDatabase(FieldValueRepository $fieldValueRepository, array $localesByCode, ?int $fieldValueId, int $entryId)
    {
        if (!$this->isValueSet) {
            throw new \Exception('Cannot save value without setting a value first');
        }
        $mergeValueIntoField = new MergeValueIntoField();

        $grandParentFieldValue = $fieldValueRepository->createFieldValue($this->id, $fieldValueId, $entryId, null, null);

        if ($this->translatable) {
            foreach ($this->value as $localeCode => $blocks) {
                $locale = $localesByCode[$localeCode];
                foreach($blocks as $keyValuesBlock) {
                    $parentFieldValue = $fieldValueRepository->createFieldValue(null, $grandParentFieldValue->id, $entryId, $locale->id, null);
                    $fields = $mergeValueIntoField->handle($keyValuesBlock, $this->fields);
                    foreach ($fields as $field) {
                        $field->createFieldValueInDatabase($fieldValueRepository, $localesByCode, $parentFieldValue->id, $entryId);
                    }
                }
            }
        } else {
            foreach($this->value as $keyValuesBlock) {
                $parentFieldValue = $fieldValueRepository->createFieldValue(null, $grandParentFieldValue->id, $entryId, null, null);
                $fields = $mergeValueIntoField->handle($keyValuesBlock, $this->fields);
                foreach ($fields as $field) {
                    $field->createFieldValueInDatabase($fieldValueRepository, $localesByCode, $parentFieldValue->id, $entryId);
                }
            }
        }
    }

    // Used in GetFieldsByEntityIdAndEntityType handler
    public function setValueFromDatabase(array $siteLocales, int $entryId): void
    {
        if(!$this->id) {
            throw new \Exception('Cannot set value on field without id');
        }

        $getFieldValuesByFieldIdAndEntryId = new GetFieldValuesByFieldIdAndEntryId(new FieldValueRepository);

        if ($this->translatable) {
            $eloquentFieldValues = $getFieldValuesByFieldIdAndEntryId->handle($this->id, $entryId);
            $values = [];

            foreach ($siteLocales as $siteLocale) {
                if ($eloquentFieldValues->where('locale_id', $siteLocale->id)->isEmpty()) {
                    $values[$siteLocale->code] = '';
                } else {
                    $values[$siteLocale->code] = $eloquentFieldValues->where('locale_id', $siteLocale->id)->first()->value;
                }
            }
            $this->setValue($values);
        } else {
            $eloquentFieldValues = $getFieldValuesByFieldIdAndEntryId->handle($this->id, $entryId);

            if ($eloquentFieldValues->isEmpty()) {
                $this->setValue('');
            } else {
                $this->setValue($eloquentFieldValues->first()->value);
            }
        }
    }

    public function isValid($value): bool
    {
        $mergeValueIntoField = new MergeValueIntoField;
        $validateValueFromField = new ValidateValueFromField;
        $getLocalesByType = new GetLocalesByType(new LocaleRepository);

        $errors = [];
        if ($this->minimum !== null && count($value) < $this->minimum) {
            $errors = ['field' => __('cyvian.errors.minimum', ['min' => $this->minimum])];
        } else if ($this->maximum !== null && count($value) > $this->maximum) {
            $errors = ['field' => __('cyvian.errors.maximum', ['max' => $this->maximum])];
        }

        $locales = $getLocalesByType->handle(Locale::IS_CMS);
        $blocksErrors = [];
        foreach ($value as $index => $keyValuesBlock) {
            $fields = $mergeValueIntoField->handle($keyValuesBlock, $this->fields);
            $blockErrors = [];
            foreach ($fields as $field) {
                $validateValueFromField->handle($field, $fields, $locales);
                if ($field->error) {
                    $blockErrors[$field->key] = $field->error;
                }
                $field->error = null;
            }
            if (count($blockErrors) > 0) {
                $blocksErrors[$index] = $blockErrors;
            }
        }
        if(!empty($blockErrors)) {
            $errors = array_merge($errors, ['blocks' => $blocksErrors]);
        }
        if(!empty($errors)) {
            $this->error = $errors;
            return false;
        }
        return true;
    }

    public function createAttributesInDatabase(FieldAttributeRepository $fieldAttributeRepository)
    {
        $bulkInsertFieldAttributes = new BulkInsertFieldAttributes($fieldAttributeRepository);

        $bulkInsertFieldAttributes->handle([
            'minimum' => $this->minimum,
            'maximum' => $this->maximum,
        ], $this->id);
    }

    public function jsonSerialize(): array
    {
        $baseAttributes = $this->getBaseAttributesArray();
        return array_merge($baseAttributes, [
            'minimum' => $this->minimum,
            'maximum' => $this->maximum,
            'fields' => $this->fields,
        ]);
    }

    public function setIsBaseField(bool $isBaseField): void
    {
        $this->isBaseField = $isBaseField;
        foreach($this->fields as $field) {
            $field->setIsBaseField($isBaseField);
        }
    }

    public function setEntryId(?int $entryId)
    {
        $this->entryId = $entryId;
        foreach($this->fields as $field) {
            $field->setEntryId($entryId);
        }
    }

    public function setTranslatable(bool $translatable): void
    {
        $this->translatable = $translatable;
        if($this->translatable) {
            foreach($this->fields as $field) {
                $field->setNotTranslatableDeep();
            }
        }
    }

    public function setNotTranslatableDeep()
    {
        $this->translatable = false;
        foreach($this->fields as $field) {
            $field->setNotTranslatableDeep();
        }
    }
}
