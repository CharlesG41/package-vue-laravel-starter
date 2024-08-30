<?php

namespace Cyvian\Src\app\Handlers\View;

use App\Managers\Manager;
use Cyvian\Src\app\Handlers\Form\GetFieldsFromForm;
use Cyvian\Src\App\Handlers\Locale\GetCurrentLocale;
use Cyvian\Src\app\Handlers\Utils\GetSanitizedActionsForList;
use Cyvian\Src\app\Repositories\ActionEntryRoleRepository;
use Cyvian\Src\app\Repositories\ActionEntryTypeRoleRepository;
use Cyvian\Src\app\Repositories\LocaleRepository;

class GetEntriesData
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

    public function handle(Manager $manager, bool $withActions): array
    {
        $getCurrentLocale = new GetCurrentLocale($this->localeRepository);
        $getFieldsFromForm = new GetFieldsFromForm;

        $currentLocale = $getCurrentLocale->handle();
        $fields = $getFieldsFromForm->handle($manager->entryType->form);

        $displayOnListFields = array_filter(
            $fields,
            function ($field) {
                return $field->displayOnList ?? false;
            }
        );

        $titles = [];
        foreach ($displayOnListFields as $displayOnListField) {
            $titles[$displayOnListField->key] = $displayOnListField->name->getCurrent();
        }

        if ($withActions) {
            $titles['actions'] = __('cyvian.general.actions');
        }

        return [
            'pluralName' => $manager->entryType->translation->pluralNames->getCurrent(),
            'singularName' => $manager->entryType->translation->singularNames->getCurrent(),
            'titles' => $titles,
            'rows' => $manager->rows($currentLocale, $withActions),
            'filters' => [],
        ];
    }
}
