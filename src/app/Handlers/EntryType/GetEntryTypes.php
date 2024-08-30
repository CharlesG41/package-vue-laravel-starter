<?php

namespace Cyvian\Src\app\Handlers\EntryType;

use Cyvian\Src\app\Repositories\ActionEntryTypeRoleRepository;
use Cyvian\Src\app\Repositories\ActionRepository;
use Cyvian\Src\app\Repositories\ActionTranslationRepository;
use Cyvian\Src\app\Repositories\EntryTypeRepository;
use Cyvian\Src\app\Repositories\EntryTypeTranslationRepository;
use Cyvian\Src\app\Repositories\FieldRepository;
use Cyvian\Src\app\Repositories\LocaleRepository;
use Cyvian\Src\app\Repositories\SectionRepository;
use Cyvian\Src\app\Repositories\SectionTranslationRepository;

class GetEntryTypes
{
    private $entryTypeRepository;
    private $actionRepository;
    private $actionTranslationRepository;
    private $actionEntryTypeRoleRepository;
    private $fieldRepository;
    private $localeRepository;
    private $entryTypeTranslationRepository;
    private $sectionRepository;
    private $sectionTranslationRepository;

    public function __construct(
        EntryTypeRepository $entryTypeRepository,
        ActionRepository $actionRepository,
        ActionTranslationRepository $actionTranslationRepository,
        ActionEntryTypeRoleRepository $actionEntryTypeRoleRepository,
        FieldRepository $fieldRepository,
        LocaleRepository $localeRepository,
        EntryTypeTranslationRepository $entryTypeTranslationRepository,
        SectionRepository $sectionRepository,
        SectionTranslationRepository $sectionTranslationRepository
    )
    {
        $this->entryTypeRepository = $entryTypeRepository;
        $this->actionRepository = $actionRepository;
        $this->actionTranslationRepository = $actionTranslationRepository;
        $this->actionEntryTypeRoleRepository = $actionEntryTypeRoleRepository;
        $this->fieldRepository = $fieldRepository;
        $this->localeRepository = $localeRepository;
        $this->entryTypeTranslationRepository = $entryTypeTranslationRepository;
        $this->sectionRepository = $sectionRepository;
        $this->sectionTranslationRepository = $sectionTranslationRepository;
    }

    public function handle(bool $withForm = true, bool $withActions = true)
    {
        $instantiateEntryTypeFromDatabaseObject = new InstantiateEntryTypeFromDatabaseObject(
            $this->actionRepository,
            $this->actionTranslationRepository,
            $this->actionEntryTypeRoleRepository,
            $this->fieldRepository,
            $this->localeRepository,
            $this->entryTypeTranslationRepository,
            $this->sectionRepository,
            $this->sectionTranslationRepository
        );
        $eloquentEntryTypes = $this->entryTypeRepository->getEntryTypes();

        $entryTypes = [];
        foreach ($eloquentEntryTypes as $eloquentEntryType) {
            $entryTypes[] = $instantiateEntryTypeFromDatabaseObject->handle($eloquentEntryType, $withForm, $withActions);
        }

        return $entryTypes;
    }
}
