<?php

namespace Cyvian\Src\app\Handlers\Entry;

use Cyvian\Src\app\Classes\Entry;
use Cyvian\Src\app\Classes\Form;
use Cyvian\Src\app\Handlers\ActionEntryRole\AddActionEntryRoleForEntry;
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
use Illuminate\Support\Facades\DB;

class CreateEntry
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
        $this->sectionRepository = $sectionRepository;
        $this->sectionTranslationRepository = $sectionTranslationRepository;
        $this->localeRepository = $localeRepository;
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
        $addActionEntryRoleForEntry = new AddActionEntryRoleForEntry($this->actionEntryRoleRepository);
        $saveForm = new SaveForm(
            $this->fieldRepository,
            $this->fieldAttributeRepository,
            $this->fieldValueRepository,
            $this->sectionRepository,
            $this->sectionTranslationRepository,
            $this->localeRepository
        );

        $form = $validateValuesFromForm->handle($entry->form);
        if ($form->hasError) {
            throw new \Exception('Form validation failed');
        }

        DB::beginTransaction();

        try {
            $eloquentEntry = $this->entryRepository->createEntry($entry);
            $entry->setId($eloquentEntry->id);
            $entry->form->setEntityId($entry->id);
            $entry->form->setEntityType(Entry::class);

            $entry = $mergeActionFieldsFromEntryIntoActions->handle($entry);
            $addActionEntryRoleForEntry->handle($entry);

            $saveForm->handle($entry->form);
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        } finally {
            DB::commit();
        }

        return new HandlerResponse($entry, true);
    }
}
