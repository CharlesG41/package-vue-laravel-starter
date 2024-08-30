<?php

namespace Cyvian\Src\app\Handlers\MenuSection;

use Cyvian\Src\app\Classes\MenuSection;
use Cyvian\Src\app\Handlers\Action\InstantiateActionFromDatabaseObject;
use Cyvian\Src\app\Handlers\MenuItem\InstantiateMenuItemFromActionAndEntryTypeId;
use Cyvian\Src\app\Handlers\MenuSection\MenuSectionTranslation\GetMenuSectionTranslationByMenuSectionId;
use Cyvian\Src\app\Handlers\User\CanUserExecuteAction;
use Cyvian\Src\App\Models\User;
use Cyvian\Src\app\Repositories\ActionEntryRoleRepository;
use Cyvian\Src\app\Repositories\ActionEntryTypeRoleRepository;
use Cyvian\Src\app\Repositories\ActionRepository;
use Cyvian\Src\app\Repositories\ActionTranslationRepository;
use Cyvian\Src\app\Repositories\EntryTypeRepository;
use Cyvian\Src\app\Repositories\FieldRepository;
use Cyvian\Src\app\Repositories\LocaleRepository;
use Cyvian\Src\app\Repositories\MenuSectionRepository;
use Cyvian\Src\app\Repositories\MenuSectionTranslationRepository;

class GetMenuSectionsByUser
{
    private $actionRepository;
    private $actionTranslationRepository;
    private $localeRepository;
    private $actionEntryTypeRoleRepository;
    private $fieldRepository;
    private $menuSectionTranslationRepository;
    private $menuSectionRepository;
    private $entryTypeRepository;

    public function __construct(
        ActionRepository $actionRepository,
        ActionTranslationRepository $actionTranslationRepository,
        ActionEntryTypeRoleRepository $actionEntryTypeRoleRepository,
        FieldRepository $fieldRepository,
        MenuSectionRepository $menuSectionRepository,
        MenuSectionTranslationRepository $menuSectionTranslationRepository,
        EntryTypeRepository $entryTypeRepository,
        LocaleRepository $localeRepository
    )
    {
        $this->actionRepository = $actionRepository;
        $this->actionTranslationRepository = $actionTranslationRepository;
        $this->actionEntryTypeRoleRepository = $actionEntryTypeRoleRepository;
        $this->fieldRepository = $fieldRepository;
        $this->menuSectionRepository = $menuSectionRepository;
        $this->menuSectionTranslationRepository = $menuSectionTranslationRepository;
        $this->entryTypeRepository = $entryTypeRepository;
        $this->localeRepository = $localeRepository;
    }

    public function handle(User $user): array
    {
        $canUserExecuteAction = new CanUserExecuteAction(
            new ActionEntryRoleRepository,
            new ActionEntryTypeRoleRepository
        );
        $instanceActionFromDatabaseObject = new InstantiateActionFromDatabaseObject(
            $this->actionTranslationRepository,
            $this->localeRepository,
            $this->actionEntryTypeRoleRepository,
            $this->fieldRepository
        );
        $getMenuSectionTranslationByMenuSectionId = new GetMenuSectionTranslationByMenuSectionId(
            $this->menuSectionTranslationRepository,
            $this->localeRepository
        );
        $instantiateMenuItemFromActionAndEntryTypeId = new InstantiateMenuItemFromActionAndEntryTypeId(
            $this->entryTypeRepository,
        );

        $eloquentMenuSections = $this->menuSectionRepository->getMenuSections();

        $menuSections = [];
        foreach ($eloquentMenuSections as $eloquentMenuSection) {
            $menuSectionTranslations = $getMenuSectionTranslationByMenuSectionId->handle($eloquentMenuSection->id);

            $eloquentActions = $this->actionRepository->getActionsByMenuSectionIdIdAndName($eloquentMenuSection->id, 'list');

            $actions = [];
            foreach ($eloquentActions as $eloquentAction) {
                $action = $instanceActionFromDatabaseObject->handle($eloquentAction, false);
                if ($canUserExecuteAction->handle($user, $action)) {
                    $actions[] = $action;
                }
            }

            $menuItems = [];
            foreach ($actions as $action) {
                $menuItems[] = $instantiateMenuItemFromActionAndEntryTypeId->handle($action);
            }

            $menuSection = new MenuSection(
                $menuSectionTranslations,
                $menuItems
            );
            $menuSection->setId($eloquentMenuSection->id);

            $menuSections[] = $menuSection;
        }

        return $menuSections;
    }
}
