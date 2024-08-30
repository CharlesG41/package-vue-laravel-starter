<?php

namespace Cyvian\Src\app\Handlers\Tab;

use Cyvian\Src\app\Classes\Entry;
use Cyvian\Src\app\Classes\EntryType;
use Cyvian\Src\app\Classes\Form;
use Cyvian\Src\app\Classes\Locale;
use Cyvian\Src\app\Classes\Tab;
use Cyvian\Src\app\Handlers\Field\CreateField;
use Cyvian\Src\app\Handlers\Locale\GetLocalesAsArrayKeyCodeByType;
use Cyvian\Src\App\Handlers\Locale\GetLocalesByType;
use Cyvian\Src\app\Handlers\Tab\TabTranslation\CreateTabTranslation;
use Cyvian\Src\app\Repositories\FieldAttributeRepository;
use Cyvian\Src\app\Repositories\FieldRepository;
use Cyvian\Src\app\Repositories\FieldValueRepository;
use Cyvian\Src\app\Repositories\LocaleRepository;
use Cyvian\Src\app\Repositories\TabRepository;
use Cyvian\Src\app\Repositories\TabTranslationRepository;

class CreateTab
{
    private $tabRepository;
    private $tabTranslationRepository;
    private $fieldRepository;
    private $fieldAttributeRepository;
    private $fieldValueRepository;
    private $localeRepository;

    public function __construct(
        TabRepository $tabRepository,
        TabTranslationRepository $tabTranslationRepository,
        FieldRepository $fieldRepository,
        FieldAttributeRepository $fieldAttributeRepository,
        FieldValueRepository $fieldValueRepository,
        LocaleRepository $localeRepository
    )
    {
        $this->tabRepository = $tabRepository;
        $this->tabTranslationRepository = $tabTranslationRepository;
        $this->fieldRepository = $fieldRepository;
        $this->fieldAttributeRepository = $fieldAttributeRepository;
        $this->fieldValueRepository = $fieldValueRepository;
        $this->localeRepository = $localeRepository;
    }

    public function handle(Form $form, Tab $tab, array $localesByCode): Tab
    {
        $createTabTranslation = new CreateTabTranslation($this->tabTranslationRepository, $this->localeRepository);

        $tab->setEntityId($form->entityId);
        $tab->setEntityType($form->entityType);
        $tab->setIsBaseTab($form->entityType === EntryType::class);
        $eloquentTab = $this->tabRepository->createTab($tab);
        $tab->setId($eloquentTab->id);
        $createTabTranslation->handle($tab->translation);

        foreach ($tab->fields as $field) {
            if (!$field->id) {
                $field->setEntityId($tab->id);
                $field->setEntityType(Tab::class);
                $field->setIsBaseField($form->entityType === EntryType::class);
                $field->createFieldInDatabase($this->fieldRepository, $this->fieldAttributeRepository);
            } else {
                $field->updateFieldInDatabase($this->fieldRepository, $this->fieldAttributeRepository);
            }
            if ($form->entityType === Entry::class) {
                $field->deleteFieldValueInDatabase($this->fieldValueRepository, $form->entityId);
                $field->createFieldValueInDatabase($this->fieldValueRepository, $localesByCode, null, $form->entityId);
            }
        }

        return $tab;
    }
}
