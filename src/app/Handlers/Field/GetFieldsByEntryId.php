<?php

namespace Cyvian\Src\app\Handlers\Field;

use Cyvian\Src\app\Classes\Entry;
use Cyvian\Src\app\Classes\EntryType;
use Cyvian\Src\app\Handlers\Tab\GetTabsByEntityIdAndEntityType;
use Cyvian\Src\app\Repositories\EntryTypeRepository;
use Cyvian\Src\app\Repositories\FieldRepository;
use Cyvian\Src\app\Repositories\LocaleRepository;
use Cyvian\Src\app\Repositories\SectionRepository;
use Cyvian\Src\app\Repositories\SectionTranslationRepository;
use Cyvian\Src\app\Repositories\TabRepository;
use Cyvian\Src\app\Repositories\TabTranslationRepository;

class GetFieldsByEntryId
{
    private $entryTypeRepository;
    private $fieldRepository;
    private $sectionRepository;
    private $sectionTranslationRepository;
    private $localeRepository;

    public function __construct(
        EntryTypeRepository $entryTypeRepository,
        FieldRepository $fieldRepository,
        SectionRepository $sectionRepository,
        SectionTranslationRepository $sectionTranslationRepository,
        LocaleRepository $localeRepository
    )
    {
        $this->entryTypeRepository = $entryTypeRepository;
        $this->fieldRepository = $fieldRepository;
        $this->sectionRepository = $sectionRepository;
        $this->sectionTranslationRepository = $sectionTranslationRepository;
        $this->localeRepository = $localeRepository;
    }

    public function handle(int $entryId)
    {
        $getFieldsByEntityTypeAndEntityId = new GetFieldsByEntityIdAndEntityType(
            $this->fieldRepository
        );

        $getTabsByEntityIdAndEntityType = new GetTabsByEntityIdAndEntityType(
            $this->sectionRepository,
            $this->sectionTranslationRepository,
            $this->localeRepository,
            $this->fieldRepository
        );

        $entryTypeId = $this->entryTypeRepository->getEntryTypeIdByEntryId($entryId);

        $entryFields = $getFieldsByEntityTypeAndEntityId->handle($entryId, Entry::class, true, $entryId);
        $entryTypeFields = $getFieldsByEntityTypeAndEntityId->handle($entryTypeId, EntryType::class, true, $entryId);

        $entryTabs = $getTabsByEntityIdAndEntityType->handle($entryId, Entry::class, true, $entryId);
        $entryTypeTabs = $getTabsByEntityIdAndEntityType->handle($entryTypeId, EntryType::class, true, $entryId);
        $tabs = array_merge($entryTypeTabs, $entryTabs);

        $tabFields = [];
        foreach($tabs as $tab) {
            $tabFields = array_merge($tabFields, $tab->fields);
        }

        return array_merge($entryTypeFields, $entryFields, $tabFields);
    }
}
