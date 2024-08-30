<?php

namespace Cyvian\Src\app\Handlers\Manager;

use App\Managers\ActionResponse;
use Cyvian\Src\app\Classes\Action;
use Cyvian\Src\app\Classes\Entry;
use Cyvian\Src\app\Classes\EntryType;
use Cyvian\Src\app\Handlers\Action\GetActionsByPositionAndEntryTypeId;
use Cyvian\Src\app\Handlers\Entry\CreateEntry;
use Cyvian\Src\app\Handlers\Form\InstantiateFormFromArray;
use Cyvian\Src\app\Handlers\Form\ValidateDuplicateKeyFromForm;
use Cyvian\Src\app\Handlers\Form\ValidateEmptyKeysFromForm;
use Cyvian\Src\app\Handlers\Utils\MergeValuesArrayIntoForm;
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
use Illuminate\Support\Facades\Auth;

class StoreModelBaseManager
{
    private $entryRepository;
    private $entryTypeRepository;
    private $entryTypeTranslationRepository;
    private $actionRepository;
    private $actionTranslationRepository;
    private $actionEntryRoleRepository;
    private $actionEntryTypeRoleRepository;
    private $fieldRepository;
    private $fieldAttributeRepository;
    private $fieldValueRepository;
    private $sectionRepository;
    private $sectionTranslationRepository;
    private $localeRepository;

    public function __construct(
        EntryRepository                $entryRepository,
        EntryTypeRepository            $entryTypeRepository,
        EntryTypeTranslationRepository $entryTypeTranslationRepository,
        ActionRepository               $actionRepository,
        ActionTranslationRepository    $actionTranslationRepository,
        ActionEntryRoleRepository      $actionEntryRoleRepository,
        ActionEntryTypeRoleRepository  $actionEntryTypeRoleRepository,
        FieldRepository                $fieldRepository,
        FieldAttributeRepository       $fieldAttributeRepository,
        FieldValueRepository           $fieldValueRepository,
        SectionRepository              $sectionRepository,
        SectionTranslationRepository   $sectionTranslationRepository,
        LocaleRepository               $localeRepository
    )
    {
        $this->entryRepository = $entryRepository;
        $this->entryTypeRepository = $entryTypeRepository;
        $this->entryTypeTranslationRepository = $entryTypeTranslationRepository;
        $this->actionRepository = $actionRepository;
        $this->actionTranslationRepository = $actionTranslationRepository;
        $this->actionEntryRoleRepository = $actionEntryRoleRepository;
        $this->actionEntryTypeRoleRepository = $actionEntryTypeRoleRepository;
        $this->fieldRepository = $fieldRepository;
        $this->fieldAttributeRepository = $fieldAttributeRepository;
        $this->fieldValueRepository = $fieldValueRepository;
        $this->sectionRepository = $sectionRepository;
        $this->sectionTranslationRepository = $sectionTranslationRepository;
        $this->localeRepository = $localeRepository;
    }

    public function handle(EntryType $entryType, array $values, array $form): ActionResponse
    {
        $mergeValuesArrayIntoForm = new MergeValuesArrayIntoForm;
        $getActionByPositionAndEntryTypeId = new GetActionsByPositionAndEntryTypeId(
            $this->actionRepository,
            $this->actionTranslationRepository,
            $this->actionEntryTypeRoleRepository,
            $this->fieldRepository,
            $this->localeRepository,
        );
        $instantiateFormFromArray = new InstantiateFormFromArray;
        $createEntry = new CreateEntry(
            $this->entryRepository,
            $this->actionEntryRoleRepository,
            $this->entryTypeRepository,
            $this->entryTypeTranslationRepository,
            $this->actionRepository,
            $this->actionTranslationRepository,
            $this->actionEntryTypeRoleRepository,
            $this->fieldRepository,
            $this->fieldAttributeRepository,
            $this->fieldValueRepository,
            $this->sectionRepository,
            $this->sectionTranslationRepository,
            $this->localeRepository
        );
        $validateEmptyKeysFromForm = new ValidateEmptyKeysFromForm;
        $validateDuplicateKeyFromForm = new ValidateDuplicateKeyFromForm;
        $validateValuesFromForm = new ValidateValuesFromForm;

        $form = $instantiateFormFromArray->handle($form['sections']);

        // check form for empty keys
        $emptyKeysResponse = $validateEmptyKeysFromForm->handle($form);
        if (!empty($emptyKeysResponse)) {
            return new ActionResponse(_('cyvian.errors.keys_empty'), ActionResponse::ERROR, 4000, false, null, $emptyKeysResponse);
        }

        // check form for duplicate keys
        $duplicateKeysResponse = $validateDuplicateKeyFromForm->handle($form);
        if (!empty($duplicateKeysResponse)) {
            return new ActionResponse(_('cyvian.errors.same_key'), ActionResponse::ERROR, 4000, false, null, $duplicateKeysResponse);
        }

        $form = $mergeValuesArrayIntoForm->handle($values, $form);

        $form = $validateValuesFromForm->handle($form);

        if ($form->hasError) {
            return new ActionResponse(_('cyvian.errors.validation_failed'), ActionResponse::ERROR, 4000, false, null, $form);
        }

        $actions = $getActionByPositionAndEntryTypeId->handle(Action::POSITION_ROW, $entryType->id);

        $entry = new Entry(
            0,
            $entryType->id,
            Auth::id(),
            Auth::id(),
            $actions,
            $form
        );

        $handlerResponse = $createEntry->handle($entry);
        if (!$handlerResponse->isSuccessful) {
            return new ActionResponse(__('cyvian.errors.validation_failed'), ActionResponse::ERROR, 4000, false, null, $handlerResponse->data);
        }
        $entry = $handlerResponse->data;

        return new ActionResponse(__('cyvian.item.stored'), 'success', 4000, false, $entry->id);
    }
}
