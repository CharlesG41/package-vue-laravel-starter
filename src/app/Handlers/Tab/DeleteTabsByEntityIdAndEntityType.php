<?php

namespace Cyvian\Src\app\Handlers\Tab;

use Cyvian\Src\app\Classes\Tab;
use Cyvian\Src\app\Handlers\Field\DeleteFieldsByEntityIdAndEntityType;
use Cyvian\Src\app\Handlers\Tab\TabTranslation\DeleteTabTranslationByTabId;
use Cyvian\Src\app\Repositories\FieldAttributeRepository;
use Cyvian\Src\app\Repositories\FieldRepository;
use Cyvian\Src\app\Repositories\FieldValueRepository;
use Cyvian\Src\app\Repositories\LocaleRepository;
use Cyvian\Src\app\Repositories\TabRepository;
use Cyvian\Src\app\Repositories\TabTranslationRepository;

class DeleteTabsByEntityIdAndEntityType
{
    private $tabRepository;
    private $tabTranslationRepository;
    private $localeRepository;
    private $fieldRepository;
    private $fieldAttributeRepository;
    private $fieldValueRepository;

    public function __construct(
        TabRepository $tabRepository,
        TabTranslationRepository $tabTranslationRepository,
        LocaleRepository $localeRepository,
        FieldRepository $fieldRepository,
        FieldAttributeRepository $fieldAttributeRepository,
        FieldValueRepository $fieldValueRepository
    )
    {
        $this->tabRepository = $tabRepository;
        $this->tabTranslationRepository = $tabTranslationRepository;
        $this->localeRepository = $localeRepository;
        $this->fieldRepository = $fieldRepository;
        $this->fieldAttributeRepository = $fieldAttributeRepository;
        $this->fieldValueRepository = $fieldValueRepository;
    }

    public function handle(int $entityId, string $entityType): void
    {
        $getTabsByEntityIdAndEntityType = new GetTabsByEntityIdAndEntityType($this->tabRepository, $this->tabTranslationRepository, $this->localeRepository, $this->fieldRepository);
        $deleteTabTranslationByTabId = new DeleteTabTranslationByTabId($this->tabTranslationRepository);
        $deleteFieldsByEntityIdAndEntityType = new DeleteFieldsByEntityIdAndEntityType($this->fieldRepository, $this->fieldAttributeRepository, $this->fieldValueRepository);

        $getTabsByEntityIdAndEntityType->handle($entityId, $entityType);




        // this should return an array of all the tabs deleted and then we can delete the fields and the translations
        $deletedTabIds = $this->tabRepository->deleteTabsByEntityIdAndEntityType($entityId, $entityType);

        foreach($deletedTabIds as $tabId) {
            $deleteTabTranslationByTabId->handle($tabId);
            $deleteFieldsByEntityIdAndEntityType->handle($tabId, Tab::class);
        }


        $tabs = $getTabsByEntityIdAndEntityType->handle($entityId, $entityType);

    }
}
