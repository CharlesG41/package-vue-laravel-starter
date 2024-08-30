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
use Cyvian\Src\app\Handlers\FieldAttribute\BulkInsertFieldAttributes;
use Cyvian\Src\app\Handlers\FieldAttribute\DeleteFieldAttributesByFieldId;
use Cyvian\Src\app\Handlers\FieldAttribute\GetFieldAttributesByFieldId;
use Cyvian\Src\app\Handlers\FieldValue\GetFieldValuesByFieldIdAndEntryId;
use Cyvian\Src\app\Handlers\FieldValue\GetFieldValuesByFieldIdAndEntryIdAndLocaleId;
use Cyvian\Src\App\Handlers\Locale\GetCurrentLocale;
use Cyvian\Src\App\Handlers\Locale\GetLocalesByType;
use Cyvian\Src\App\Models\Cyvian\Field as EloquentField;
use Cyvian\Src\app\Repositories\FieldAttributeRepository;
use Cyvian\Src\app\Repositories\FieldRepository;
use Cyvian\Src\app\Repositories\FieldValueRepository;
use Cyvian\Src\app\Repositories\LocaleRepository;
use Cyvian\Src\App\Utils\Localisation;

class Sections extends BaseField implements FieldInterface, \JsonSerializable
{
    const TYPE = 'sections';

    public $minimum;
    public $maximum;

    public function __construct(
        string $key,
        Localisation $name,
        Localisation $description,
        bool $translatable,
        ?int $minimum,
        ?int $maximum,
        array $conditions,
        FieldPermissions $fieldPermissions,
        bool $locked = false
    )
    {
        $this->minimum = $minimum;
        $this->maximum = $maximum;

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
        $conditions = parent::instantiateConditionsFromArray($data['conditions']);
        $fieldPermissions = parent::instantiateFieldPermissionsFromArray($data);

        $field = new self(
            $data['key'],
            new Localisation($data['name']),
            new Localisation($data['description']),
            $data['translatable'],
            $data['minimum'],
            $data['maximum'],
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
        $fieldAttributesAsKeyValue = $getFieldAttributesByFieldId->handle($eloquentField->id);

        $conditions = parent::instantiateConditionsFromArray(json_decode($eloquentField->conditions, true));
        $fieldPermissions = parent::instantiateFieldPermissionsFromEloquent($eloquentField);
        $field = new self(
            $eloquentField->key,
            new Localisation(json_decode($eloquentField->name, true)),
            new Localisation(json_decode($eloquentField->description, true)),
            $eloquentField->translatable,
            $fieldAttributesAsKeyValue['minimum'],
            $fieldAttributesAsKeyValue['maximum'],
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

        if ($this->translatable) {
            foreach ($this->value as $localeCode => $value) {
                $locale = $localesByCode[$localeCode];
            }
        } else {
            $fieldValueRepository->createFieldValue($this->id, $fieldValueId, $entryId, null, $this->value);
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
        // TODO, validate sections
        if ($this->minimum && count($value) < $this->minimum) {
            $this->error = __('cyvian.errors.fields.minimum');
            return false;
        }

        if ($this->maximum && count($value) > $this->maximum) {
            $this->error = __('cyvian.errors.fields.maximum');
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
        ]);
    }
}
