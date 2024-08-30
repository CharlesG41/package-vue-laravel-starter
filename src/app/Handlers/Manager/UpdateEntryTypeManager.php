<?php

namespace Cyvian\Src\app\Handlers\Manager;

use App\Managers\ActionResponse;
use Cyvian\Src\app\Classes\EntryType as NewEntryType;
use Cyvian\Src\app\Classes\Translations\EntryTypeTranslation as NewEntryTypeTranslation;
use Cyvian\Src\app\Handlers\Action\InstantiateActionFromArray;
use Cyvian\Src\app\Handlers\EntryType\UpdateEntryType;
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

class UpdateEntryTypeManager
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
        EntryRepository $entryRepository,
        EntryTypeRepository $entryTypeRepository,
        EntryTypeTranslationRepository $entryTypeTranslationRepository,
        ActionRepository $actionRepository,
        ActionTranslationRepository $actionTranslationRepository,
        ActionEntryRoleRepository $actionEntryRoleRepository,
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

    public function handle(array $entryTypeFormArray, array $values, int $id)
    {
        $mergeValuesArrayIntoForm = new MergeValuesArrayIntoForm();
        $instantiateFormFromArray = new InstantiateFormFromArray;
        $validateValuesFromForm = new ValidateValuesFromForm;
        $updateEntryType = new UpdateEntryType(
            $this->entryRepository,
            $this->entryTypeRepository,
            $this->entryTypeTranslationRepository,
            $this->actionRepository,
            $this->actionTranslationRepository,
            $this->actionEntryRoleRepository,
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

        // check if another entrytype exists with the same name
        $isEntryTypeNameValid = $validateEntryTypeName->handle($values['name'], [$id]);
        if (!$isEntryTypeNameValid) {
            return new ActionResponse(_('cyvian.exceptions.name_unique'), ActionResponse::ERROR, 4000, false, null, ['name' => _('cyvian.exceptions.name_unique')]);
        }

        // instantiate entryTypeForm
        $entryTypeForm = $instantiateFormFromArray->handle(
            $entryTypeFormArray['sections']
        );

        $entryTypeForm->sections[0]->fields[0]->error = ['fr' => "Error fr"];

        return new ActionResponse(_('cyvian.errors.keys_empty'), ActionResponse::ERROR, 4000, false, null, $entryTypeForm);

        // check form for empty keys
        $emptyKeysResponse = $validateEmptyKeysFromForm->handle($entryTypeForm);
        if (!empty($emptyKeysResponse)) {
            return new ActionResponse(_('cyvian.errors.keys_empty'), ActionResponse::ERROR, 4000, false, null, $emptyKeysResponse);
        }

        // check form for duplicate keys
        $duplicateKeysResponse = $validateDuplicateKeyFromForm->handle($entryTypeForm);
        if (!empty($duplicateKeysResponse)) {
            return new ActionResponse(_('cyvian.errors.same_key'), ActionResponse::ERROR, 4000, false, null, $duplicateKeysResponse);
        }

        // merge data
        $entryTypeForm = $mergeValuesArrayIntoForm->handle($values, $entryTypeForm);

        // validate that entryTypeForm has every values
        $handlerResponse = $validateValuesFromForm->handle($entryTypeForm);
        if (!$handlerResponse->isSuccessful) {
            return new ActionResponse(_('cyvian.errors.validation_failed'), ActionResponse::ERROR, 4000, false, null, $handlerResponse->data);
        }

        // instantiate actions for the entryType we were editing
        $actionsToInstantiate = array_merge($values['top_actions'], $values['row_actions']);
        $actions = [];
        foreach($actionsToInstantiate as $action) {
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

        $form->setEntityId($id);
        $form->setEntityType(NewEntryType::class);

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
        $entryType->setId($id);

        $updateEntryType->handle($entryType);

        return new ActionResponse(__('cyvian.item.stored'), 'success', 4000, false, $entryType->id);
    }
}
