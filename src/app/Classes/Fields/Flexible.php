<?php

namespace Cyvian\Src\app\Classes\Fields;

use Cyvian\Src\app\Classes\Fields\Classes\BaseField;
use Cyvian\Src\app\Classes\Fields\Classes\Condition;
use Cyvian\Src\app\Classes\Fields\Classes\FieldInterface;
use Cyvian\Src\app\Classes\Fields\Classes\FieldPermissions;
use Cyvian\Src\app\Classes\Fields\Classes\FlexibleSection;
use Cyvian\Src\app\Classes\Fields\Classes\ValidationResponse;
use Cyvian\Src\app\Classes\Fields\Traits\HasFields;
use Cyvian\Src\app\Classes\Locale;
use Cyvian\Src\app\Classes\ValueForList;
use Cyvian\Src\app\Handlers\Field\GetFieldById;
use Cyvian\Src\app\Handlers\Field\GetFieldsByEntityIdAndEntityType;
use Cyvian\Src\app\Handlers\FieldAttribute\BulkInsertFieldAttributes;
use Cyvian\Src\app\Handlers\FieldAttribute\DeleteFieldAttributesByFieldId;
use Cyvian\Src\app\Handlers\FieldAttribute\GetFieldAttributesByFieldId;
use Cyvian\Src\app\Handlers\FieldValue\GetFieldValuesByFieldIdAndEntryId;
use Cyvian\Src\app\Handlers\FieldValue\GetFieldValuesByFieldIdAndEntryIdAndLocaleId;
use Cyvian\Src\app\Handlers\FieldValue\GetFieldValuesByFieldValueId;
use Cyvian\Src\app\Handlers\FlexibleSection\CreateFlexibleSectionFieldValue;
use Cyvian\Src\app\Handlers\FlexibleSection\GetFlexibleSectionsByFieldId;
use Cyvian\Src\app\Handlers\FlexibleSection\InstantiateFlexibleSectionFromArray;
use Cyvian\Src\App\Handlers\Locale\GetCurrentLocale;
use Cyvian\Src\App\Handlers\Locale\GetLocalesByType;
use Cyvian\Src\App\Models\Cyvian\Field as EloquentField;
use Cyvian\Src\app\Repositories\FieldAttributeRepository;
use Cyvian\Src\app\Repositories\FieldRepository;
use Cyvian\Src\app\Repositories\FieldValueRepository;
use Cyvian\Src\app\Repositories\FlexibleSectionRepository;
use Cyvian\Src\app\Repositories\LocaleRepository;
use Cyvian\Src\App\Utils\Localisation;

class Flexible extends BaseField implements FieldInterface, \JsonSerializable
{
    const TYPE = 'flexible';

    public $sections;
    public $minimum;
    public $maximum;

    public function __construct(
        string $key,
        Localisation $name,
        Localisation $description,
        bool $translatable,
        ?int $minimum,
        ?int $maximum,
        array $sections,
        array $conditions,
        FieldPermissions $fieldPermissions,
        bool $locked = false
    )
    {
        foreach ($sections as $section) {
            if (!$section instanceof FlexibleSection) {
                throw new \InvalidArgumentException('Section must be an instance of FlexibleSection');
            }
        }
        $this->minimum = $minimum;
        $this->maximum = $maximum;
        $this->sections = $sections;
        $this->value = [];

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
        $instantiateFlexibleSectionFromArray = new InstantiateFlexibleSectionFromArray;

        $conditions = parent::instantiateConditionsFromArray($data['conditions']);
        $fieldPermissions = parent::instantiateFieldPermissionsFromArray($data);

        $sections = [];
        foreach ($data['sections'] as $section) {
            $sections[] = $instantiateFlexibleSectionFromArray->handle($section);
        }

        $field = new self(
            $data['key'],
            new Localisation($data['name']),
            new Localisation($data['description']),
            $data['translatable'],
            $data['minimum'],
            $data['maximum'],
            $sections,
            $conditions,
            $fieldPermissions,
            $data['locked'],
        );

        $field->baseInstantiateFromArray($data);

        if (array_key_exists('id', $data)) {
            foreach ($field->sections as $section) {
                $section->setFieldId($field->id);
            }
        }

        return $field;
    }

    static public function instantiateFromEloquentField(EloquentField $eloquentField, ?int $entryId): self
    {
        $getFieldAttributesByFieldId = new GetFieldAttributesByFieldId(new FieldAttributeRepository);
        $fieldAttributesAsKeyValue = $getFieldAttributesByFieldId->handle($eloquentField->id);

        $getFlexibleSectionsByFieldId = new GetFlexibleSectionsByFieldId(new FlexibleSectionRepository, new FieldRepository);

        $sections = $getFlexibleSectionsByFieldId->handle($eloquentField->id, $entryId);

        $conditions = parent::instantiateConditionsFromArray(json_decode($eloquentField->conditions, true));
        $fieldPermissions = parent::instantiateFieldPermissionsFromEloquent($eloquentField);
        $field = new self(
            $eloquentField->key,
            new Localisation(json_decode($eloquentField->name, true)),
            new Localisation(json_decode($eloquentField->description, true)),
            $eloquentField->translatable,
            $fieldAttributesAsKeyValue['minimum'],
            $fieldAttributesAsKeyValue['maximum'],
            $sections,
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
        $flexibleSectionRepository = new FlexibleSectionRepository;

        parent::baseCreateFieldInDatabase($fieldRepository);
        $this->createAttributesInDatabase($fieldAttributeRepository);

        foreach ($this->sections as $section) {
            $section->setFieldId($this->id);
            $eloquentFlexibleSection = $flexibleSectionRepository->createFlexibleSection($section);
            foreach($section->fields as $field) {
                // if flexible section is translatable, then no need for the child fields to be translatable,
                if ($this->translatable) {
                  $field->translatable = false;
                }
                $field->setEntityId($eloquentFlexibleSection->id);
                $field->setEntityType(FlexibleSection::class);
                $field->createFieldInDatabase($fieldRepository, $fieldAttributeRepository);
            }
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
        $createFlexibleSectionValue = new CreateFlexibleSectionFieldValue(
            $fieldValueRepository,
        );

        if (!$this->isValueSet) {
            throw new \Exception('Cannot save value without setting a value first');
        }

        if ($this->translatable) {
            foreach ($this->value as $localeCode => $value) {
                $locale = $localesByCode[$localeCode];
                $createFlexibleSectionValue->handle($localesByCode, $this->sections, $value, $this->id, $fieldValueId, $locale->id, $entryId);
            }
        } else {
            $createFlexibleSectionValue->handle($localesByCode, $this->sections, $this->value, $this->id, $fieldValueId, null, $entryId);
        }
    }

    // Used in GetFieldsByEntityIdAndEntityType handler
    public function setValueFromDatabase(array $siteLocales, int $entryId): void
    {
        if(!$this->id) {
            throw new \Exception('Cannot set value on field without id');
        }
       $getFieldValueByFieldIdAndEntryId = new GetFieldValuesByFieldIdAndEntryId(new FieldValueRepository);
       $getFieldValueByFieldIdAndLocaleIdAndEntryId = new GetFieldValuesByFieldIdAndEntryIdAndLocaleId(new FieldValueRepository);
       $getFieldValuesByFieldValueId = new GetFieldValuesByFieldValueId(new FieldValueRepository);
       $getFieldById = new GetFieldById(new FieldRepository);

        $values = [];
        if ($this->translatable) {
            foreach($siteLocales as $locale) {
                $values[$locale->code] = [];
                $parentFieldValues = $getFieldValueByFieldIdAndLocaleIdAndEntryId->handle($this->id, $locale->id, $entryId);
                foreach($parentFieldValues as $parentFieldValue) {
                    $block = [
                        'key' => $parentFieldValue->value,
                    ];
                    $sectionValues = [];
                    $eloquentFieldValues = $getFieldValuesByFieldValueId->handle($parentFieldValue->id);
                    foreach($eloquentFieldValues as $eloquentFieldValue) {
                        $field = $getFieldById->handle($eloquentFieldValue->field_id, $siteLocales, true, $entryId);
                        $sectionValues[$field->key] = $eloquentFieldValue->value;
                    }
                    $block['value'] = $sectionValues;
                    $values[$locale->code][] = $block;
                }
            }
        } else {
            $parentFieldValues = $getFieldValueByFieldIdAndLocaleIdAndEntryId->handle($this->id, $locale->id, $entryId);
            foreach($parentFieldValues as $parentFieldValue) {
                $block = [
                    'key' => $parentFieldValue->value,
                ];
                $sectionValues = [];
                $eloquentFieldValues = $getFieldValuesByFieldValueId->handle($parentFieldValue->id);
                foreach($eloquentFieldValues as $eloquentFieldValue) {
                    $field = $getFieldById->handle($eloquentFieldValue->field_id, $siteLocales, true, $entryId);
                    $sectionValues[$field->key] = $eloquentFieldValue->value;
                }
                $block['value'] = $sectionValues;
                $values[] = $block;
            }
        }

        $this->setValue($values);
    }

    public function setValue($value): void
    {
        if ($this->translatable) {
            foreach($value as $localeCode => &$localeValue) {
                if ($localeValue === null) {
                    $localeValue = [];
                }
            }
        } else {
            if ($value === null) {
                $value = [];
            }
        }
        $this->value = $value;
        $this->isValueSet = true;
    }

    public function isValid($value): bool
    {
        if ($this->minimum !== null && count($value) < $this->minimum) {
            $this->error = __('cyvian.errors.number.minimum', ['min' => $this->minimum]);
            return false;
        }

        if ($this->maximum !== null && count($value) > $this->maximum) {
            $this->error = __('cyvian.errors.number.maximum', ['max' => $this->maximum]);
            return false;
        }

        return true;
    }

    public function createAttributesInDatabase(FieldAttributeRepository $fieldAttributeRepository)
    {
        $bulkInsertFieldAttributes = new BulkInsertFieldAttributes($fieldAttributeRepository);

        $bulkInsertFieldAttributes->handle([
            'position' => $this->position,
            'minimum' => $this->minimum,
            'maximum' => $this->maximum,
        ], $this->id);
    }

    public function jsonSerialize(): array
    {
        $baseAttributes = $this->getBaseAttributesArray();
        return array_merge($baseAttributes, [
            'sections' => $this->sections,
            'position' => $this->position,
            'minimum' => $this->minimum,
            'maximum' => $this->maximum,
        ]);
    }

    public function setIsBaseField(bool $isBaseField): void
    {
        $this->isBaseField = $isBaseField;
        foreach($this->sections as $section) {
            foreach($section->fields as $field) {
                $field->setIsBaseField($isBaseField);
            }
        }
    }

    public function setEntryId(?int $entryId)
    {
        $this->entryId = $entryId;
        foreach($this->sections as $section) {
            foreach($section->fields as $field) {
                $field->setEntryId($entryId);
            }
        }
    }

    public function setTranslatable(bool $translatable): void
    {
        $this->translatable = $translatable;
        if ($this->translatable) {
            foreach($this->sections as $section) {
                foreach($section->fields as $field) {
                    $field->setNotTranslatableDeep();
                }
            }
        }
    }

    public function setNotTranslatableDeep()
    {
        $this->translatable = false;
        foreach($this->sections as $section) {
            foreach($section->fields as $field) {
                $field->setNotTranslatableDeep();
            }
        }
    }
}
