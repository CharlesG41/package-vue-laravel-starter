<?php

namespace Cyvian\Src\app\Handlers\Entry;

use Cyvian\Src\app\Classes\Entry;
use Cyvian\Src\app\Repositories\ActionEntryTypeRoleRepository;
use Cyvian\Src\app\Repositories\ActionRepository;
use Cyvian\Src\app\Repositories\ActionTranslationRepository;
use Cyvian\Src\app\Repositories\EntryRepository;
use Cyvian\Src\app\Repositories\FieldRepository;
use Cyvian\Src\app\Repositories\LocaleRepository;
use Cyvian\Src\app\Repositories\SectionRepository;
use Cyvian\Src\app\Repositories\SectionTranslationRepository;
use Cyvian\Src\app\Repositories\TabRepository;
use Cyvian\Src\app\Repositories\TabTranslationRepository;

class GetEntryById
{
    private $entryRepository;
    private $actionRepository;
    private $actionTranslationRepository;
    private $actionEntryTypeRoleRepository;
    private $fieldRepository;
    private $sectionRepository;
    private $sectionTranslationRepository;
    private $localeRepository;

    public function __construct(
        EntryRepository $entryRepository,
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
        $this->actionRepository = $actionRepository;
        $this->actionTranslationRepository = $actionTranslationRepository;
        $this->actionEntryTypeRoleRepository = $actionEntryTypeRoleRepository;
        $this->fieldRepository = $fieldRepository;
        $this->sectionRepository = $sectionRepository;
        $this->sectionTranslationRepository = $sectionTranslationRepository;
        $this->localeRepository = $localeRepository;
    }

    public function handle(int $entryId): Entry
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

        $eloquentEntry = $this->entryRepository->getEntryById($entryId);
        if (!$eloquentEntry) {
            throw new \Exception('Entry not found');
        }

        return $instantiateEntryFromDatabaseObject->handle($eloquentEntry);
    }
}
