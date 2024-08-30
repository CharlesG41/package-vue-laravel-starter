<?php

namespace Cyvian\Src\app\Handlers\Utils;

use Cyvian\Src\app\Classes\Action;
use Cyvian\Src\app\Classes\Entry;
use Cyvian\Src\app\Handlers\Entry\GetEntryIdsByEntryType;
use Cyvian\Src\app\Handlers\EntryType\GetEntryTypeByName;
use Cyvian\Src\app\Handlers\Form\GetFieldFromKeyNameFromForm;
use Cyvian\Src\app\Repositories\ActionEntryTypeRoleRepository;
use Cyvian\Src\app\Repositories\ActionRepository;
use Cyvian\Src\app\Repositories\ActionTranslationRepository;
use Cyvian\Src\app\Repositories\EntryRepository;
use Cyvian\Src\app\Repositories\EntryTypeRepository;
use Cyvian\Src\app\Repositories\EntryTypeTranslationRepository;
use Cyvian\Src\app\Repositories\FieldRepository;
use Cyvian\Src\app\Repositories\LocaleRepository;
use Cyvian\Src\app\Repositories\SectionRepository;
use Cyvian\Src\app\Repositories\SectionTranslationRepository;

class MergeActionFieldsFromEntryIntoActions
{
    private $entryTypeRepository;
    private $entryRepository;
    private $entryTypeTranslationRepository;
    private $actionRepository;
    private $actionTranslationRepository;
    private $actionEntryTypeRoleRepository;
    private $fieldRepository;
    private $sectionRepository;
    private $sectionTranslationRepository;
    private $localeRepository;

    public function __construct(
        EntryTypeRepository $entryTypeRepository,
        EntryRepository $entryRepository,
        EntryTypeTranslationRepository $entryTypeTranslationRepository,
        ActionRepository $actionRepository,
        ActionTranslationRepository $actionTranslationRepository,
        ActionEntryTypeRoleRepository $actionEntryTypeRoleRepository,
        FieldRepository $fieldRepository,
        SectionRepository $sectionRepository,
        SectionTranslationRepository $sectionTranslationRepository,
        LocaleRepository $localeRepository
    )
    {
        $this->entryTypeRepository = $entryTypeRepository;
        $this->entryRepository = $entryRepository;
        $this->entryTypeTranslationRepository = $entryTypeTranslationRepository;
        $this->actionRepository = $actionRepository;
        $this->actionTranslationRepository = $actionTranslationRepository;
        $this->actionEntryTypeRoleRepository = $actionEntryTypeRoleRepository;
        $this->fieldRepository = $fieldRepository;
        $this->sectionRepository = $sectionRepository;
        $this->sectionTranslationRepository = $sectionTranslationRepository;
        $this->localeRepository = $localeRepository;
    }

    // if the entry has fields related to action (action_entry_role), put the roles into the actions and remove the fields
    public function handle(Entry $entry): Entry
    {
        if ($entry->id === null) {
            throw new \Exception('Entry id is not set');
        }
        $getEntryTypeByName = new GetEntryTypeByName(
            $this->entryTypeRepository,
            $this->entryTypeTranslationRepository,
            $this->actionRepository,
            $this->actionTranslationRepository,
            $this->actionEntryTypeRoleRepository,
            $this->fieldRepository,
            $this->localeRepository,
            $this->sectionRepository,
            $this->sectionTranslationRepository
        );
        $getEntryIdsByEntryType = new GetEntryIdsByEntryType($this->entryRepository);
        $getFieldFromKeyNameFromForm = new GetFieldFromKeyNameFromForm;

        $roleEntryType = $getEntryTypeByName->handle('role');
        $allRoleIds = $getEntryIdsByEntryType->handle($roleEntryType);
        foreach ($entry->actions as &$action) {
            if ($action->rolesByEntry) {
                $actionKey = Action::FIELD_KEY . $action->name;
                $field = $getFieldFromKeyNameFromForm->handle($entry->form, $actionKey);
                if (!$field->isValueSet) {
                    throw new \Exception('Field value is not set');
                }
                $roleIds = $field->value;
                if (empty($roleIds)) {
                    $roleIds = $allRoleIds;
                }
                $action->roleIds = $roleIds;
            }
        }

        return $entry;
    }

}
