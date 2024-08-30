<?php

namespace Cyvian\Src\app\Handlers\View;

use App\Managers\Manager;
use Cyvian\Src\app\Handlers\Filter\GetFiltersFromEntryType;
use Cyvian\Src\app\Handlers\Form\GetFieldsFromForm;
use Cyvian\Src\App\Handlers\Locale\GetCurrentLocale;
use Cyvian\Src\app\Handlers\Utils\GetSanitizedActionsForList;
use Cyvian\Src\app\Repositories\ActionEntryRoleRepository;
use Cyvian\Src\app\Repositories\ActionEntryTypeRoleRepository;
use Cyvian\Src\app\Repositories\LocaleRepository;
use Illuminate\Support\Facades\Auth;

class GetListData
{
    private $actionEntryRoleRepository;
    private $actionEntryTypeRoleRepository;
    private $localeRepository;

    public function __construct(
        ActionEntryRoleRepository $actionEntryRoleRepository,
        ActionEntryTypeRoleRepository $actionEntryTypeRoleRepository,
        LocaleRepository $localeRepository
    )
    {
        $this->actionEntryRoleRepository = $actionEntryRoleRepository;
        $this->actionEntryTypeRoleRepository = $actionEntryTypeRoleRepository;
        $this->localeRepository = $localeRepository;
    }

    public function handle(Manager $manager, bool $withActions = true, bool $withFilters = true): array
    {
        $getCurrentLocale = new GetCurrentLocale($this->localeRepository);
        $getSanitizedActionsForList = new GetSanitizedActionsForList(
            $this->actionEntryRoleRepository,
            $this->actionEntryTypeRoleRepository
        );
        $getFiltersFromEntryType = new GetFiltersFromEntryType;

        $currentLocale = $getCurrentLocale->handle();

        $getFieldsFromForm = new GetFieldsFromForm;
        $fields = $getFieldsFromForm->handle($manager->entryType->form);

        $fields = array_filter(
            $fields,
            function ($field) {
                return $field->displayOnList ?? false;
            }
        );

        $titles = [];
        foreach ($fields as $field) {
            $titles[$field->key] = $field->name->getCurrent();
        }
        $titles['actions'] = __('cyvian.general.actions');

        $actions = [];
        if ($withActions) {
            $actions = $getSanitizedActionsForList->handle($manager->entryType->actions, $manager->entryType->name, null, Auth::user());
        }

        $filters = [];
        if ($withFilters) {
            $filters = $getFiltersFromEntryType->handle($manager->entryType, $currentLocale);
        }

        return [
            'singular_name' => $manager->entryType->translation->singularNames->getCurrent(),
            'plural_name' => $manager->entryType->translation->pluralNames->getCurrent(),
            'titles' => $titles,
            'entryTypeName' => $manager->entryType->name,
            'rows' => $manager->rows($currentLocale, $withActions),
            'filters' => $filters,
            'actions' => $actions,
            'entryTypeId' => $manager->entryType->id
        ];
    }
}
