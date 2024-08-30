<?php

namespace Cyvian\Src\app\Handlers\MenuItem;

use App\Managers\Manager;
use Cyvian\Src\app\Classes\Action;
use Cyvian\Src\app\Classes\EntryType;
use Cyvian\Src\app\Classes\MenuItem;
use Cyvian\Src\app\Repositories\EntryTypeRepository;

class InstantiateMenuItemFromActionAndEntryTypeId
{
    private $entryTypeRepository;

    public function __construct(
        EntryTypeRepository $entryTypeRepository,
    )
    {
        $this->entryTypeRepository = $entryTypeRepository;
    }

    public function handle(Action $action)
    {
        $eloquentEntryType = $this->entryTypeRepository->getEntryTypeById($action->entryTypeId);

        $manager = Manager::getManagerObject($eloquentEntryType->name, false, false);

        if ($manager->entryType->type == EntryType::TYPE_SETTING) {
            $url = $manager->menuUrl($eloquentEntryType->entries->first());
        } else {
            $url = $manager->menuUrl();
        }

        return new MenuItem(
            $manager->entryType->translation->pluralNames,
            $url
        );
    }
}
