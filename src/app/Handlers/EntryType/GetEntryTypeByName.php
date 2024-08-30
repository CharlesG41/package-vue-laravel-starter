<?php

namespace Cyvian\Src\app\Handlers\EntryType;

use Cyvian\Src\app\Classes\EntryType;
use Cyvian\Src\app\Repositories\ActionEntryTypeRoleRepository;
use Cyvian\Src\app\Repositories\ActionRepository;
use Cyvian\Src\app\Repositories\ActionTranslationRepository;
use Cyvian\Src\app\Repositories\EntryTypeRepository;
use Cyvian\Src\app\Repositories\EntryTypeTranslationRepository;
use Cyvian\Src\app\Repositories\FieldRepository;
use Cyvian\Src\app\Repositories\LocaleRepository;
use Cyvian\Src\app\Repositories\SectionRepository;
use Cyvian\Src\app\Repositories\SectionTranslationRepository;

class GetEntryTypeByName
{
    private $entryTypeRepository;
    private $actionRepository;
    private $actionTranslationRepository;
    private $actionEntryTypeRoleRepository;
    private $fieldRepository;
    private $localeRepository;
    private $sectionRepository;
    private $sectionTranslationRepository;
    private $entryTypeTranslationRepository;

    public function __construct(
        EntryTypeRepository $entryTypeRepository,
        EntryTypeTranslationRepository $entryTypeTranslationRepository,
        ActionRepository $actionRepository,
        ActionTranslationRepository $actionTranslationRepository,
        ActionEntryTypeRoleRepository $actionEntryTypeRoleRepository,
        FieldRepository $fieldRepository,
        LocaleRepository $localeRepository,
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

    public function handle(string $name, bool $withForm = true, bool $withActions = true): EntryType
    {
        $eloquentEntryType = $this->entryTypeRepository->getEntryTypeByName($name);
        if (!$eloquentEntryType) {
            throw new \Exception('Entry type not found');
        }
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

        return $instantiateEntryTypeFromDatabaseObject->handle($eloquentEntryType, $withForm, $withActions);
    }
}
