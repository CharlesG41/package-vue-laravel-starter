<?php

namespace Cyvian\Src\app\Handlers\Entry;

use Cyvian\Src\app\Classes\Entry;
use Cyvian\Src\app\Handlers\ActionEntryRole\AddActionEntryRoleForEntry;
use Cyvian\Src\app\Handlers\ActionEntryRole\DeleteActionEntryRoleByEntryId;
use Cyvian\Src\app\Handlers\Form\SaveForm;
use Cyvian\Src\app\Handlers\HandlerResponse;
use Cyvian\Src\app\Handlers\Utils\MergeActionFieldsFromEntryIntoActions;
use Cyvian\Src\app\Handlers\Utils\ValidateValuesFromForm;
use Cyvian\Src\app\Repositories\ActionEntryRoleRepository;
use Cyvian\Src\app\Repositories\ActionEntryTypeRoleRepository;
use Cyvian\Src\app\Repositories\ActionRepository;
use Cyvian\Src\app\Repositories\ActionTranslationRepository;
use Cyvian\Src\app\Repositories\EntryRepository;
use Cyvian\Src\app\Repositories\EntryTypeRepository;
use Cyvian\Src\app\Repositories\EntryTypeTranslationRepository;
use Cyvian\Src\app\Repositories\FieldAttributeRepository;
use Cyvian\Src\app\Repositories\FieldRepository;
use Cyvian\Src\app\Repositories\FieldValueRepository;
use Cyvian\Src\app\Repositories\LocaleRepository;
use Cyvian\Src\app\Repositories\SectionRepository;
use Cyvian\Src\app\Repositories\SectionTranslationRepository;
use Cyvian\Src\app\Repositories\TabRepository;
use Cyvian\Src\app\Repositories\TabTranslationRepository;

class UpdateEntry
{
    private $entryRepository;
    private $actionEntryRoleRepository;
    private $entryTypeRepository;
    private $entryTypeTranslationRepository;
    private $actionRepository;
    private $actionTranslationRepository;
    private $actionEntryTypeRoleRepository;
    private $fieldRepository;
    private $fieldAttributeRepository;
    private $fieldValueRepository;
    private $sectionRepository;
    private $sectionTranslationRepository;
    private $localeRepository;

    public function __construct(
        EntryRepository $entryRepository,
        ActionEntryRoleRepository $actionEntryRoleRepository,
        EntryTypeRepository $entryTypeRepository,
        EntryTypeTranslationRepository $entryTypeTranslationRepository,
        ActionRepository $actionRepository,
        ActionTranslationRepository $actionTranslationRepository,
        ActionEntryTypeRoleRepository $actionEntryTypeRoleRepository,
        FieldRepository $fieldRepository,
        FieldAttributeRepository $fieldAttributeRepository,
        FieldValueRepository $fieldValueRepository,
        SectionRepository $sectionRepository,
        SectionTranslationRepository $sectionTranslationRepository,
        LocaleRepository $localeRepository
    )
    {
        $this->entryRepository = $entryRepository;
        $this->actionEntryRoleRepository = $actionEntryRoleRepository;
        $this->entryTypeRepository = $entryTypeRepository;
        $this->entryTypeTranslationRepository = $entryTypeTranslationRepository;
        $this->actionRepository = $actionRepository;
        $this->actionTranslationRepository = $actionTranslationRepository;
        $this->actionEntryTypeRoleRepository = $actionEntryTypeRoleRepository;
        $this->fieldRepository = $fieldRepository;
        $this->fieldAttributeRepository = $fieldAttributeRepository;
        $this->fieldValueRepository = $fieldValueRepository;
        $this->localeRepository = $localeRepository;
        $this->sectionRepository = $sectionRepository;
        $this->sectionTranslationRepository = $sectionTranslationRepository;
    }

    public function handle(Entry $entry): HandlerResponse
    {
        $validateValuesFromForm = new ValidateValuesFromForm;
        $mergeActionFieldsFromEntryIntoActions = new MergeActionFieldsFromEntryIntoActions(
            $this->entryTypeRepository,
            $this->entryRepository,
            $this->entryTypeTranslationRepository,
            $this->actionRepository,
            $this->actionTranslationRepository,
            $this->actionEntryTypeRoleRepository,
            $this->fieldRepository,
            $this->sectionRepository,
            $this->sectionTranslationRepository,
            $this->localeRepository
        );
        $deleteActionEntryRoleByEntryId = new DeleteActionEntryRoleByEntryId($this->actionEntryRoleRepository);
        $addActionEntryRoleForEntry = new AddActionEntryRoleForEntry($this->actionEntryRoleRepository);
        $saveForm = new SaveForm(
            $this->fieldRepository,
            $this->fieldAttributeRepository,
            $this->fieldValueRepository,
            $this->sectionRepository,
            $this->sectionTranslationRepository,
            $this->localeRepository
        );

        if (!$entry->id){
            throw new \Exception('Entry not found');
        }

        $handlerResponse = $validateValuesFromForm->handle($entry->form);
        if (!$handlerResponse->isSuccessful) {
            return $handlerResponse;
        }

        $this->entryRepository->updateEntry($entry);
        $entry->form->setEntityId($entry->id);
        $entry->form->setEntityType(Entry::class);

        $entry = $mergeActionFieldsFromEntryIntoActions->handle($entry);
        $deleteActionEntryRoleByEntryId->handle($entry->id);
        $addActionEntryRoleForEntry->handle($entry);

        $saveForm->handle($entry->form);

        return new HandlerResponse($entry, true);
    }
}
