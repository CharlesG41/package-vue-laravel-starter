<?php

namespace Cyvian\Src\app\Handlers\Entry;

use Cyvian\Src\app\Classes\Entry;
use Cyvian\Src\app\Handlers\Field\GetFieldsByEntryId;
use Cyvian\Src\App\Handlers\Locale\GetCurrentLocale;
use Cyvian\Src\app\Handlers\Section\GetSectionsByEntityIdAndEntityType;
use Cyvian\Src\app\Handlers\Section\GetSectionsByEntryId;
use Cyvian\Src\app\Repositories\ActionEntryTypeRoleRepository;
use Cyvian\Src\app\Repositories\ActionRepository;
use Cyvian\Src\app\Repositories\ActionTranslationRepository;
use Cyvian\Src\app\Repositories\EntryRepository;
use Cyvian\Src\app\Repositories\EntryTypeRepository;
use Cyvian\Src\app\Repositories\FieldRepository;
use Cyvian\Src\app\Repositories\LocaleRepository;
use Cyvian\Src\app\Repositories\SectionRepository;
use Cyvian\Src\app\Repositories\SectionTranslationRepository;

class GetEntryValuesById
{
    private $entryRepository;
    private $entryTypeRepository;
    private $actionRepository;
    private $actionTranslationRepository;
    private $actionEntryTypeRoleRepository;
    private $fieldRepository;
    private $sectionRepository;
    private $sectionTranslationRepository;
    private $localeRepository;

    public function __construct(
        EntryRepository $entryRepository,
        EntryTypeRepository $entryTypeRepository,
        ActionRepository $actionRepository,
        ActionTranslationRepository $actionTranslationRepository,
        ActionEntryTypeRoleRepository $actionEntryTypeRoleRepository,
        FieldRepository $fieldRepository,
        SectionRepository $sectionRepository,
        SectionTranslationRepository $sectionTranslationRepository,
        LocaleRepository $localeRepository
    )
    {
        $this->entryRepository = $entryRepository;
        $this->entryTypeRepository = $entryTypeRepository;
        $this->actionRepository = $actionRepository;
        $this->actionTranslationRepository = $actionTranslationRepository;
        $this->actionEntryTypeRoleRepository = $actionEntryTypeRoleRepository;
        $this->fieldRepository = $fieldRepository;
        $this->sectionRepository = $sectionRepository;
        $this->sectionTranslationRepository = $sectionTranslationRepository;
        $this->localeRepository = $localeRepository;
    }

    public function handle(int $id): array
    {
        $instantiateEntryFromDatabaseObject = new InstantiateEntryFromDatabaseObject(
            $this->actionRepository,
            $this->actionTranslationRepository,
            $this->actionEntryTypeRoleRepository,
            $this->fieldRepository,
            $this->sectionRepository,
            $this->sectionTranslationRepository,
            $this->localeRepository
        );
        $getCurrentLocale = new GetCurrentLocale($this->localeRepository);

        $currentLocale = $getCurrentLocale->handle();

        $eloquentEntry = $this->entryRepository->getEntryById($id);
        $entry = $instantiateEntryFromDatabaseObject->handle($eloquentEntry, true, false, true);

        $values = [];

        foreach ($entry->form->sections as $section) {
            if ($section->key === null) {
                foreach($section->fields as $field) {
                    $values[$field->key] = $field->getTranslatedValue($currentLocale->code);
                }
            } else {
                $sectionValues = [];
                foreach($section->fields as $field) {
                    $sectionValues[$field->key] = $field->getTranslatedValue($currentLocale->code);

                }
                $values[$section->key] = $sectionValues;
            }
        }

        return $values;
    }
}
