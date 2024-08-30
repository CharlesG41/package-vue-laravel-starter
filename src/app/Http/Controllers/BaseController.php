<?php

namespace Cyvian\Src\app\Http\Controllers;

use App\Managers\Manager;
use Cyvian\Src\app\Handlers\Entry\GetEntryById;
use Cyvian\Src\app\Handlers\EntryType\GetEntryTypeById;
use Cyvian\Src\app\Handlers\EntryType\GetEntryTypes;
use Cyvian\Src\app\Handlers\Form\GetDefaultFieldValueFromForm;
use Cyvian\Src\App\Handlers\Locale\GetCurrentLocale;
use Cyvian\Src\App\Handlers\Locale\GetLocalesByType;
use Cyvian\Src\app\Handlers\MenuSection\GetMenuSectionsByUser;
use Cyvian\Src\app\Handlers\View\GetEntriesData;
use Cyvian\Src\app\Handlers\View\GetListData;
use Cyvian\Src\App\Models\Cyvian\EntryType;
use Cyvian\Src\App\Models\Cyvian\Locale;
use Cyvian\Src\App\Models\Cyvian\News;
use Cyvian\Src\App\Models\Cyvian\Page;
use Cyvian\Src\app\Repositories\ActionEntryRoleRepository;
use Cyvian\Src\app\Repositories\ActionEntryTypeRoleRepository;
use Cyvian\Src\app\Repositories\ActionRepository;
use Cyvian\Src\app\Repositories\ActionTranslationRepository;
use Cyvian\Src\app\Repositories\EntryRepository;
use Cyvian\Src\app\Repositories\EntryTypeRepository;
use Cyvian\Src\app\Repositories\EntryTypeTranslationRepository;
use Cyvian\Src\app\Repositories\FieldRepository;
use Cyvian\Src\app\Repositories\LocaleRepository;
use Cyvian\Src\app\Repositories\MenuSectionRepository;
use Cyvian\Src\app\Repositories\MenuSectionTranslationRepository;
use Cyvian\Src\app\Repositories\SectionRepository;
use Cyvian\Src\app\Repositories\SectionTranslationRepository;
use Cyvian\Src\app\Repositories\TabRepository;
use Cyvian\Src\app\Repositories\TabTranslationRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;

class BaseController extends Controller
{
    // get the data to display the menu
    public function menu(Request $request): JsonResponse
    {
        $getMenuSectionsByUser = new GetMenuSectionsByUser(
            new ActionRepository,
            new ActionTranslationRepository,
            new ActionEntryTypeRoleRepository,
            new FieldRepository,
            new MenuSectionRepository,
            new MenuSectionTranslationRepository,
            new EntryTypeRepository,
            new LocaleRepository
        );
        $menuSections = $getMenuSectionsByUser->handle(Auth::user());

        return response()->json([
            'items' => $menuSections
        ]);
    }

    // get the data to display the entryTypes inside the entryTypeModal
    public function entryTypes(Request $request): JsonResponse
    {
        if ($request->query('ids')) {
            $entryTypes = EntryType::find($request->input('ids'));
        } else {
            $entryTypes = EntryType::where('type', 'model')->get();
        }
        return response()->json(
            $entryTypes->mapWithKeys(function ($entryType) {
                return [$entryType->id => $entryType->translation->plural_name];
            })
        );
    }

    // get the data to display the list inside the entryTypeModal
    public function getListData(Request $request, int $entryTypeId): JsonResponse
    {
        $getEntriesData = new GetEntriesData(
            new ActionEntryRoleRepository,
            new ActionEntryTypeRoleRepository,
            new LocaleRepository
        );

        $data = $getEntriesData->handle(Manager::getManagerObject(EntryType::find($entryTypeId)->name), false);

        return response()->json($data);
    }

    public function rows(Request $request, int $entryTypeId): JsonResponse
    {
        $getCurrentLocale = new GetCurrentLocale(new LocaleRepository);
        $currentLocale = $getCurrentLocale->handle();
        $entryType = EntryType::find($entryTypeId);
        $manager = Manager::getManagerObject($entryType->name);

        return response()->json([
            'rows' => $manager->getFilteredRows($request->all(), $currentLocale)
        ]);
    }

    public function switchLocale(Request $request, string $locale)
    {
        session(['locale' => $locale]);
        Config::set('locales.current_locale', Locale::where('code', session('locale'))->get()->first());
        return redirect(route('manager.create', ['entryType' => 'news']));
    }

    public function entriesForFields(Request $request, string $ids): JsonResponse
    {
        $getEntryById = new GetEntryById(
            new EntryRepository,
            new ActionRepository,
            new ActionTranslationRepository,
            new ActionEntryTypeRoleRepository,
            new FieldRepository,
            new SectionRepository,
            new SectionTranslationRepository,
            new LocaleRepository
        );
        $getEntryTypeById = new GetEntryTypeById(
            new EntryTypeRepository,
            new EntryTypeTranslationRepository,
            new ActionRepository,
            new ActionTranslationRepository,
            new ActionEntryTypeRoleRepository,
            new FieldRepository,
            new LocaleRepository,
            new SectionRepository,
            new SectionTranslationRepository
        );
        $getLocalesByType = new GetLocalesByType(
            new LocaleRepository
        );
        $getCurrentLocale = new GetCurrentLocale(
            new LocaleRepository
        );
        $getDefaultFieldValueFromForm = new GetDefaultFieldValueFromForm;

        $data = [];
        $ids = explode(',', $ids);
        $siteLocales = $getLocalesByType->handle(\Cyvian\Src\app\Classes\Locale::IS_SITE);
        $currentLocale = $getCurrentLocale->handle();

        foreach ($ids as $id) {
            $entry = $getEntryById->handle($id);
            $entryType = $getEntryTypeById->handle($entry->entryTypeId);

            $data[] = [
                'id' => $entry->id,
                'label' => $getDefaultFieldValueFromForm->handle($entry->form, $siteLocales, $currentLocale->code, $entry->id),
                'singular_name' => $entryType->translation->singularNames->getCurrent()
            ];
        }

        return response()->json(['entries' => $data]);
    }

    public function entriesForFieldsEntryTypeOnly(Request $request, string $ids): JsonResponse
    {
        $entries = EntryType::whereIn('id', explode(',', $ids))->get()->map(function ($entryType) {
            return [
                'id' => $entryType->id,
                'label' => $entryType->translation->singular_name,
            ];
        });
        return response()->json(['entries' => $entries]);
    }

    public function page(Request $request, string $slug)
    {
        // dd(Page::where('slug', $slug)->get()->first());
        // return view('News', [
        //     'news' => News::where('slug', $slug)->get()->first(),
        // ]);
        // dd(
        //     'test',
        //     Auth::id(),
        //     Page::find(1),
        //     News::where('slug', $slug)->get()->first(),
        //     'Query count : ' . count(DB::getQueryLog()),
        // );
        $news = News::only('title', 'image', 'url')->where('category', Category::where('name', $categorySlug)->get())->get();

        Cyvian::createEntryType()
            ->addField()
            ->addField()
            ->addField();

        Cyvian::createEntry('entry_type',)
            ->addField();
    }
}
