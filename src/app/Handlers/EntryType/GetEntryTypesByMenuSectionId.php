<?php

namespace Cyvian\Src\app\Handlers\EntryType;

use Cyvian\Src\app\Repositories\ActionEntryTypeRoleRepository;
use Cyvian\Src\app\Repositories\ActionRepository;
use Cyvian\Src\app\Repositories\ActionTranslationRepository;
use Cyvian\Src\app\Repositories\EntryTypeRepository;
use Cyvian\Src\app\Repositories\EntryTypeTranslationRepository;
use Cyvian\Src\app\Repositories\FieldRepository;
use Cyvian\Src\app\Repositories\LocaleRepository;
use Cyvian\Src\app\Repositories\TabRepository;
use Cyvian\Src\app\Repositories\TabTranslationRepository;

class GetEntryTypesByMenuSectionId
{
    private $entryTypeRepository;
    private $entryTypeTranslationRepository;
    private $actionRepository;
    private $actionTranslationRepository;
    private $actionEntryTypeRoleRepository;
    private $fieldRepository;
    private $tabRepository;
    private $tabTranslationRepository;
    private $localeRepository;

    public function __construct(
        EntryTypeRepository $entryTypeRepository,
        EntryTypeTranslationRepository $entryTypeTranslationRepository,
        ActionRepository $actionRepository,
        ActionTranslationRepository $actionTranslationRepository,
        ActionEntryTypeRoleRepository $actionEntryTypeRoleRepository,
        FieldRepository $fieldRepository,
        TabRepository $tabRepository,
        TabTranslationRepository $tabTranslationRepository,
        LocaleRepository $localeRepository
    )
    {
        $this->entryTypeRepository = $entryTypeRepository;
        $this->entryTypeTranslationRepository = $entryTypeTranslationRepository;
        $this->actionRepository = $actionRepository;
        $this->actionTranslationRepository = $actionTranslationRepository;
        $this->actionEntryTypeRoleRepository = $actionEntryTypeRoleRepository;
        $this->fieldRepository = $fieldRepository;
        $this->tabRepository = $tabRepository;
        $this->tabTranslationRepository = $tabTranslationRepository;
        $this->localeRepository = $localeRepository;
    }

    public function handle(int $menuSectionId)
    {
        $instantiateEntryTypeFromDatabaseObject = new InstantiateEntryTypeFromDatabaseObject(
            $this->actionRepository,
            $this->actionTranslationRepository,
            $this->actionEntryTypeRoleRepository,
            $this->fieldRepository,
            $this->localeRepository,
            $this->entryTypeTranslationRepository,
            $this->tabRepository,
            $this->tabTranslationRepository,
        );

        $eloquentEntryTypes = $this->entryTypeRepository->getEntryTypesByMenuSectionId($menuSectionId);

        $entryTypes = [];
        foreach($eloquentEntryTypes as $eloquentEntryType) {
            $entryTypes[] = $instantiateEntryTypeFromDatabaseObject->handle($eloquentEntryType, false, false);
        }

        return $entryTypes;
    }
}
