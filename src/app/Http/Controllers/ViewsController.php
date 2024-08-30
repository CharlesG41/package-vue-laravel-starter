<?php

namespace Cyvian\Src\App\Http\Controllers;

use App\Managers\ActionResponse;
use App\Managers\Manager;
use Cyvian\Src\app\Classes\Locale;
use Cyvian\Src\app\Handlers\FieldGroup\GetFieldGroupsByEntryTypeId;
use Cyvian\Src\app\Handlers\Locale\GetLocalesAsArrayKeyCodeByType;
use Cyvian\Src\app\Handlers\View\GetListData;
use Cyvian\Src\App\Models\Cyvian\EntryType;
use Cyvian\Src\app\Repositories\ActionEntryRoleRepository;
use Cyvian\Src\app\Repositories\ActionEntryTypeRoleRepository;
use Cyvian\Src\app\Repositories\FieldGroupRepository;
use Cyvian\Src\app\Repositories\FieldGroupTranslationRepository;
use Cyvian\Src\app\Repositories\FieldRepository;
use Cyvian\Src\app\Repositories\LocaleRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class ViewsController extends Controller
{
    public function login()
    {
        return Inertia::render('Login');
    }

    public function index(Request $request, string $entryType)
    {
        $manager = Manager::getManagerObject($entryType);

        if (!Auth::user()->can('list', $manager->entryType)) {
            return parent::fail(['message' => 'cyvian.messages.forbidden'], 403);
        }

        $getListData = new GetListData(
            new ActionEntryRoleRepository,
            new ActionEntryTypeRoleRepository,
            new LocaleRepository
        );
        $data = $getListData->handle($manager);

        return Inertia::render('Index', $data);
    }

    public function create(Request $request, string $entryType)
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

        return Inertia::render('Create', [
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
        ]);
    }

    public function edit(Request $request, string $entryType, int $id)
    {
        $getLocalesAsArrayKeyCodeByType = new GetLocalesAsArrayKeyCodeByType(new LocaleRepository);
        $getFieldGroupsByEntryTypeId = new GetFieldGroupsByEntryTypeId(
            new FieldGroupRepository,
            new FieldGroupTranslationRepository,
            new LocaleRepository,
            new FieldRepository
        );

        $manager = Manager::getManagerObject($entryType);
        $entry = $manager->getEntryById($id);

        if ($entry === null) {
            return $this->setActionResponseInSessionAndBack(
                new ActionResponse('cyvian.messages.not_found', 'error')
            );
        }

        if (!Auth::user()->can('edit', $manager->entryType, $entry->id)) {
            return $this->setActionResponseInSessionAndBack(
                new ActionResponse('cyvian.messages.forbidden', 'error')
            );
        }

        $locales = $getLocalesAsArrayKeyCodeByType->handle(Locale::IS_SITE);
        $locales = array_map(function($locale) {
            return strToUpper($locale->code);
        }, $locales);

        $fieldGroups = $getFieldGroupsByEntryTypeId->handle($manager->entryType->id);

        $form = $manager->form($entry);


        dump(
            json_decode(json_encode($manager->getValuesWithTranslations($id)), true)
        );

        return Inertia::render('Create', [
            'singularName' => $manager->entryType->translation->singularNames->getCurrent(),
            'form' => $form,
            'storeUrl' => '/' . $manager->entryType->name . '/update/' . $entry->id,
            'values' => $manager->getValuesWithTranslations($id),
            'fieldGroups' => $fieldGroups,
            'locales' => $locales,
            'entryType' => $entryType,
            'roleEntryTypeId' => EntryType::where('name', 'role')->get()->first()->id,
            'entryTypeEntryTypeId' => EntryType::where('name', 'entry_type')->get()->first()->id,
            'action' => 'create',
            'roleIds' => array_column(Auth::user()->roles, 'id')
        ]);
    }
}
