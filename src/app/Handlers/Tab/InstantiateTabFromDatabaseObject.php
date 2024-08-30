<?php

namespace Cyvian\Src\app\Handlers\Tab;

use Cyvian\Src\app\Classes\EntryType;
use Cyvian\Src\app\Handlers\Field\GetFieldsByEntityIdAndEntityType;
use Cyvian\Src\app\Handlers\Tab\TabTranslation\GetTabTranslationByTabId;
use Cyvian\Src\App\Models\Cyvian\Tab as EloquentTab;
use Cyvian\Src\app\Classes\Tab;
use Cyvian\Src\app\Repositories\FieldRepository;
use Cyvian\Src\app\Repositories\LocaleRepository;
use Cyvian\Src\app\Repositories\TabRepository;
use Cyvian\Src\app\Repositories\TabTranslationRepository;

class InstantiateTabFromDatabaseObject
{
    private $tabTranslationRepository;
    private $localeRepository;
    private $fieldRepository;

    public function __construct(
        TabTranslationRepository $tabTranslationRepository,
        LocaleRepository $localeRepository,
        FieldRepository $fieldRepository
    )
    {
        $this->tabTranslationRepository = $tabTranslationRepository;
        $this->localeRepository = $localeRepository;
        $this->fieldRepository = $fieldRepository;
    }

    public function handle(EloquentTab $eloquentTab, bool $setValues = false, int $entryId = null): Tab
    {
        $getTabTranslationByTabId = new GetTabTranslationByTabId($this->tabTranslationRepository, $this->localeRepository);
        $getFieldsByEntityIdAndEntityType = new GetFieldsByEntityIdAndEntityType($this->fieldRepository);

        $tabTranslation = $getTabTranslationByTabId->handle($eloquentTab->id);

        $fields = $getFieldsByEntityIdAndEntityType->handle($eloquentTab->id, Tab::class, $setValues, $entryId);

        $tab = new Tab(
            $tabTranslation,
            $fields,
        );
        $tab->setId($eloquentTab->id);
        $tab->setEntityId($eloquentTab->entity_id);
        $tab->setEntityType($eloquentTab->entity_type);
        $tab->setIsBaseTab($eloquentTab->entity_type === EntryType::class);

        return $tab;
    }
}
