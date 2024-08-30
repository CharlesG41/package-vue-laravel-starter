<?php

namespace Cyvian\Src\app\Handlers\View;

use App\Managers\ActionResponse;
use App\Managers\Manager;
use Cyvian\Src\app\Classes\Locale;
use Cyvian\Src\app\Handlers\FieldGroup\GetFieldGroupsByEntryTypeId;
use Cyvian\Src\app\Handlers\Locale\GetLocalesAsArrayKeyCodeByType;
use Cyvian\Src\App\Models\Cyvian\EntryType;
use Cyvian\Src\app\Repositories\FieldGroupRepository;
use Cyvian\Src\app\Repositories\FieldGroupTranslationRepository;
use Cyvian\Src\app\Repositories\FieldRepository;
use Cyvian\Src\app\Repositories\LocaleRepository;
use Illuminate\Support\Facades\Auth;

class GetCreateData
{
    public function __construct()
    {
        $this->fieldGroupRepository = $this->field
    }

    public function handle()
    {
        $getLocalesAsArrayKeyCodeByType = new GetLocalesAsArrayKeyCodeByType(new LocaleRepository);
        $getFieldGroupsByEntryTypeId = new GetFieldGroupsByEntryTypeId(
            new FieldGroupRepository,
            new FieldGroupTranslationRepository,
            new LocaleRepository,
            new FieldRepository
        );

        $manager = Manager::getManagerObject($entryType);

        if (!Auth::user()->can('create', $manager->entryType)) {
            return $this->setActionResponseInSessionAndBack(
                new ActionResponse('cyvian.messages.forbidden', 'error')
            );
        }

        $locales = $getLocalesAsArrayKeyCodeByType->handle(Locale::IS_SITE);
        $locales = array_map(function($locale) {
            return strToUpper($locale->code);
        }, $locales);

        $fieldGroups = $getFieldGroupsByEntryTypeId->handle($manager->entryType->id);

        $form = $manager->form();

        return [
            'singularName' => $manager->entryType->translation->singularNames->getCurrent(),
            'form' => $form,
            'storeUrl' => route('manager.store', ['entryType' => $manager->entryType->name]),
            'fieldGroups' => $fieldGroups,
            'locales' => $locales,
            'entryType' => $entryType,
            'roleEntryTypeId' => EntryType::where('name', 'role')->get()->first()->id,
            'entryTypeEntryTypeId' => EntryType::where('name', 'entry_type')->get()->first()->id,
            'action' => 'create',
            'roleIds' => array_column(Auth::user()->roles, 'id')
        ];
    }
}
