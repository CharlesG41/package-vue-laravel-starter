<?php

namespace Cyvian\Src\app\Handlers\Manager;

use App\Managers\ActionResponse;
use Cyvian\Src\app\Classes\Action;
use Cyvian\Src\app\Classes\EntryType as NewEntryType;
use Cyvian\Src\app\Classes\Translations\EntryTypeTranslation as NewEntryTypeTranslation;
use Cyvian\Src\app\Handlers\Action\BaseActions\CreateListAction;
use Cyvian\Src\app\Handlers\Action\BaseActions\CreateModifyFieldsAction;
use Cyvian\Src\app\Handlers\Action\InstantiateActionFromArray;
use Cyvian\Src\app\Handlers\EntryType\CreateEntryType;
use Cyvian\Src\app\Handlers\EntryType\ValidateEntryTypeName;
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
use Cyvian\Src\app\Repositories\TabRepository;
use Cyvian\Src\app\Repositories\TabTranslationRepository;
use Cyvian\Src\app\Utils\Localisation;

class StoreEntryTypeManager
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
        SectionRepository                  $sectionRepository,
        SectionTranslationRepository       $sectionTranslationRepository,
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

    public function handle(array $entryTypeFormArray, array $values)
    {
        $mergeValuesArrayIntoForm = new MergeValuesArrayIntoForm;
        $instantiateFormFromArray = new InstantiateFormFromArray;
        $validateValuesFromForm = new ValidateValuesFromForm;
        $createEntryType = new CreateEntryType(
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
        $instantiateActionFromArray = new InstantiateActionFromArray;
        $validateEntryTypeName = new ValidateEntryTypeName($this->entryTypeRepository);
        $validateEmptyKeysFromForm = new ValidateEmptyKeysFromForm;
        $validateDuplicateKeyFromForm = new ValidateDuplicateKeyFromForm;

        // instantiate entryTypeForm
        $entryTypeForm = $instantiateFormFromArray->handle(
            $entryTypeFormArray['sections']
        );

        // check form for empty keys
        $emptyKeys = $validateEmptyKeysFromForm->handle($entryTypeForm);
        if (!empty($emptyKeys)) {
            return new ActionResponse(_('cyvian.errors.keys_empty'), ActionResponse::ERROR, 4000, false, null, $emptyKeys);
        }

        // check form for duplicate keys
        $duplicateKeys = $validateDuplicateKeyFromForm->handle($entryTypeForm);
        if (!empty($duplicateKeys)) {
            return new ActionResponse(_('cyvian.errors.same_key'), ActionResponse::ERROR, 4000, false, null, $duplicateKeys);
        }

        // merge data
        $entryTypeForm = $mergeValuesArrayIntoForm->handle($values, $entryTypeForm);

        // validate that entryTypeForm has every values
        $handlerResponse = $validateValuesFromForm->handle($entryTypeForm);
        if (!$handlerResponse->isSuccessful) {
            return new ActionResponse(_('cyvian.errors.validation_failed'), ActionResponse::ERROR, 4000, false, null, $handlerResponse->data);
        }

        // check if another entryType exists with the same name
        $isEntryTypeNameValid = $validateEntryTypeName->handle($values['name'], []);
        if (!$isEntryTypeNameValid) {
            return new ActionResponse(_('cyvian.exceptions.name_unique'), ActionResponse::ERROR, 4000, false, null, ['name' => _('cyvian.exceptions.name_unique')]);
        }

        // instantiate actions for the entryType we were editing
        foreach ($values['actions']['top_actions'] as &$action) {
            $action['position'] = Action::POSITION_TOP;
        }

        foreach ($values['actions']['row_actions'] as &$action) {
            $action['position'] = Action::POSITION_ROW;
        }

        $actionsToInstantiate = array_merge($values['actions']['top_actions'], $values['actions']['row_actions']);
        $actions = [];
        foreach ($actionsToInstantiate as $action) {
            $actions[] = $instantiateActionFromArray->handle($action);
        }

        // instantiate the base form of the entryType we were editing
        $form = $instantiateFormFromArray->handle(
            $values['sections']
        );

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

        $listAction = new Action(
            'list',
            Action::POSITION_GENERAL,
            ACTION::ACTION_TYPE_ADMIN,
            null,
            false,
            null,
            $values['actions']['general_actions']['list_roles'],
            []
        );
        $modifyFieldsAction = new Action(
            'modify_fields',
            Action::POSITION_GENERAL,
            ACTION::ACTION_TYPE_ADMIN,
            null,
            false,
            null,
            $values['actions']['general_actions']['modify_fields_roles'],
            []
        );
        $actions[] = $listAction;
        $actions[] = $modifyFieldsAction;

        $entryType = new NewEntryType(
            $values['name'],
            $values['type'],
            $values['menu_section'],
            new NewEntryTypeTranslation(
                new Localisation($values['singular_name']),
                new Localisation($values['plural_name'])
            ),
            $form,
            $actions,
        );

        $entryType = $createEntryType->handle($entryType, true);

        return new ActionResponse(__('cyvian.item.stored'), 'success', 4000, false, $entryType->id);
    }
}
