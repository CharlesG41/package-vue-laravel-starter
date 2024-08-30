<?php

namespace Cyvian\Src\app\Handlers\EntryType;

use Cyvian\Src\app\Classes\EntryType;
use Cyvian\Src\app\Classes\Form;
use Cyvian\Src\app\Handlers\Action\GetActionsByEntryTypeId;
use Cyvian\Src\app\Handlers\EntryType\EntryTypeTranslation\GetEntryTypeTranslationsByEntryTypeId;
use Cyvian\Src\app\Handlers\Form\GetFormByEntityIdAndEntityType;
use Cyvian\Src\App\Models\Cyvian\EntryType as EloquentEntryType;
use Cyvian\Src\app\Repositories\ActionEntryTypeRoleRepository;
use Cyvian\Src\app\Repositories\ActionRepository;
use Cyvian\Src\app\Repositories\ActionTranslationRepository;
use Cyvian\Src\app\Repositories\EntryTypeTranslationRepository;
use Cyvian\Src\app\Repositories\FieldRepository;
use Cyvian\Src\app\Repositories\LocaleRepository;
use Cyvian\Src\app\Repositories\SectionRepository;
use Cyvian\Src\app\Repositories\SectionTranslationRepository;
use Cyvian\Src\app\Repositories\TabRepository;
use Cyvian\Src\app\Repositories\TabTranslationRepository;

class InstantiateEntryTypeFromDatabaseObject
{
    private $actionRepository;
    private $actionTranslationRepository;
    private $actionEntryTypeRoleRepository;
    private $fieldRepository;
    private $localeRepository;
    private $entryTypeTranslationRepository;
    private $sectionRepository;
    private $sectionTranslationRepository;

    public function __construct(
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
        $this->actionRepository = $actionRepository;
        $this->actionTranslationRepository = $actionTranslationRepository;
        $this->actionEntryTypeRoleRepository = $actionEntryTypeRoleRepository;
        $this->fieldRepository = $fieldRepository;
        $this->localeRepository = $localeRepository;
        $this->sectionRepository = $sectionRepository;
        $this->sectionTranslationRepository = $sectionTranslationRepository;
        $this->entryTypeTranslationRepository = $entryTypeTranslationRepository;
    }

    public function handle(EloquentEntryType $eloquentEntryType, bool $withForm = true, bool $withActions = true): EntryType
    {
        $getActionsByEntryTypeId = new GetActionsByEntryTypeId(
            $this->actionRepository,
            $this->actionTranslationRepository,
            $this->actionEntryTypeRoleRepository,
            $this->fieldRepository,
            $this->localeRepository
        );
        $getEntryTypeTranslationByEntryTypeId = new GetEntryTypeTranslationsByEntryTypeId(
            $this->entryTypeTranslationRepository,
            $this->localeRepository
        );
        $getFormByEntityIdAndEntityType = new GetFormByEntityIdAndEntityType(
            $this->sectionRepository,
            $this->sectionTranslationRepository,
            $this->fieldRepository,
            $this->localeRepository
        );

        $actions = [];
        if ($withActions) {
            $actions = $getActionsByEntryTypeId->handle($eloquentEntryType->id);
        }

        $entryTypeTranslation = $getEntryTypeTranslationByEntryTypeId->handle($eloquentEntryType->id);

        $form = new Form([], [], []);
        if ($withForm) {
            $form = $getFormByEntityIdAndEntityType->handle($eloquentEntryType->id, EntryType::class, false);
        }

        $entryType = new EntryType(
            $eloquentEntryType->name,
            $eloquentEntryType->type,
            $eloquentEntryType->menu_section_id,
            $entryTypeTranslation,
            $form,
            $actions
        );
        $entryType->setId($eloquentEntryType->id);

        return $entryType;
    }
}
