<?php

namespace Cyvian\Src\app\Handlers\Entry;

use Cyvian\Src\app\Classes\Action;
use Cyvian\Src\app\Classes\Entry;
use Cyvian\Src\app\Classes\EntryType;
use Cyvian\Src\app\Handlers\Action\GetActionsByPositionAndEntryTypeId;
use Cyvian\Src\app\Handlers\Form\GetFormByEntityIdAndEntityType;
use Cyvian\Src\app\Handlers\Utils\MergeForm;
use Cyvian\Src\App\Models\Cyvian\Entry as EloquentEntry;
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

class InstantiateEntryFromDatabaseObject
{
    private $actionRepository;
    private $actionTranslationRepository;
    private $actionEntryTypeRoleRepository;
    private $fieldRepository;
    private $sectionRepository;
    private $sectionTranslationRepository;
    private $localeRepository;

    public function __construct(
        ActionRepository $actionRepository,
        ActionTranslationRepository $actionTranslationRepository,
        ActionEntryTypeRoleRepository $actionEntryTypeRoleRepository,
        FieldRepository $fieldRepository,
        SectionRepository $sectionRepository,
        SectionTranslationRepository $sectionTranslationRepository,
        LocaleRepository $localeRepository
    )
    {
        $this->actionRepository = $actionRepository;
        $this->actionTranslationRepository = $actionTranslationRepository;
        $this->actionEntryTypeRoleRepository = $actionEntryTypeRoleRepository;
        $this->sectionRepository = $sectionRepository;
        $this->sectionTranslationRepository = $sectionTranslationRepository;
        $this->fieldRepository = $fieldRepository;
        $this->localeRepository = $localeRepository;
    }

    public function handle(EloquentEntry $eloquentEntry, bool $withForm = true, bool $withActions = true, bool $setValues = false): Entry
    {
        $getFormByEntityIdAndEntityType = new GetFormByEntityIdAndEntityType(
            $this->sectionRepository,
            $this->sectionTranslationRepository,
            $this->fieldRepository,
            $this->localeRepository
        );
        $getActionsByPositionAndEntryTypeId = new GetActionsByPositionAndEntryTypeId(
            $this->actionRepository,
            $this->actionTranslationRepository,
            $this->actionEntryTypeRoleRepository,
            $this->fieldRepository,
            $this->localeRepository
        );
        $mergeForm = new MergeForm();

        $actions = [];
        if ($withActions) {
            $actions = $getActionsByPositionAndEntryTypeId->handle(Action::POSITION_ROW, $eloquentEntry->entry_type_id);
        }

        $form = null;
        if ($withForm) {
            $entryTypeForm = $getFormByEntityIdAndEntityType->handle($eloquentEntry->entry_type_id, EntryType::class, $setValues, $eloquentEntry->id);
            $entryForm = $getFormByEntityIdAndEntityType->handle($eloquentEntry->id, Entry::class, $setValues, $eloquentEntry->id);

            $form = $mergeForm->handle($entryTypeForm, $entryForm);
            $form->entityId = $eloquentEntry->id;
            $form->entityType = Entry::class;
        }

        $entry = new Entry(
            $eloquentEntry->order,
            $eloquentEntry->entry_type_id,
            $eloquentEntry->created_by,
            $eloquentEntry->updated_by,
            $actions,
            $form
        );
        $entry->setId($eloquentEntry->id);

        return $entry;
    }
}
