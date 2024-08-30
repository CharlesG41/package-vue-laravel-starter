<?php

namespace Cyvian\Src\App\Models\Cyvian;

use App\Managers\ActionResponse;
use Cyvian\Src\app\Classes\Action;
use Cyvian\Src\App\Classes\Entry;
use Cyvian\Src\app\Classes\Form;
use Cyvian\Src\app\Handlers\Action\GetActionsByPositionAndEntryTypeId;
use Cyvian\Src\app\Handlers\Entry\CreateEntry;
use Cyvian\Src\app\Handlers\EntryType\GetEntryTypeByName;
use Cyvian\Src\app\Handlers\Form\InstantiateFormFromArray;
use Cyvian\Src\app\Handlers\Form\ValidateDuplicateKeyFromForm;
use Cyvian\Src\app\Handlers\Form\ValidateEmptyKeysFromForm;
use Cyvian\Src\app\Handlers\Utils\MergeValuesArrayIntoForm;
use Cyvian\Src\App\Models\User;
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
use Cyvian\Src\App\Utils\Constant;
use Cyvian\Src\App\Utils\EntryHelper;
use Cyvian\Src\App\Utils\FieldHelper;
use Cyvian\Src\App\Utils\Helper;
use Cyvian\Src\App\Utils\QueryBuilder;
use Cyvian\Src\App\Utils\TabHelper;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class BaseModel
{
    public function __construct($values = null)
    {
        foreach ($values as $key => $value) {
            $this->{$key} = $value;
        }
    }

    static private function getChildClassPath()
    {
        if(static::$entry_type == 'file') {
            return Constant::CLASS_NAME_CYVIAN_SRC_APP_MODELS_CYVIAN . ucfirst(Helper::snakeToCamel(static::$entry_type));
        }
        return Constant::CLASS_NAME_CYVIAN_SRC_APP_MODELS . ucfirst(Helper::snakeToCamel(static::$entry_type));
    }

    static public function find($id)
    {
        if (is_array($id)) {
            $entries = collect();
            foreach ($id as $i) {
                $entries->add(self::find($i));
            }

            return $entries;
        } else {
//            $entry = Entry::find($id);
            $class = self::getChildClassPath();

            return new $class($entry);
        }
    }

    static public function where(string $key, string $comparator, $value = null): QueryBuilder
    {
        $queryBuilder = QueryBuilder::instance(static::$entry_type, self::getChildClassPath());
        return $queryBuilder->where($key, $comparator, $value);
    }

    static public function whereNull(string $key): QueryBuilder
    {
        $queryBuilder = QueryBuilder::instance(static::$entry_type, self::getChildClassPath());

        return $queryBuilder->whereNull($key);
    }

    static public function all(): Collection
    {
        $entryType = self::entryType();

//        return Entry::where('entry_type_id', $entryType->id)->get()->map(function ($entry) {
//            return $entry->values;
//        });
    }

    static public function only(array $keys): QueryBuilder
    {
        $queryBuilder = QueryBuilder::instance(static::$entry_type, self::getChildClassPath());

        return $queryBuilder->only($keys);
    }

    static public function entryType(): EntryType
    {
        return EntryType::where('name', static::$entry_type)->get()->first();
    }

    static public function create(array $values, ?Form $form = null, ?array $actions = [])
    {
        $entryRepository = new EntryRepository;
        $mergeValuesArrayIntoForm = new MergeValuesArrayIntoForm;
        $createEntry = new CreateEntry(
            $entryRepository,
            new ActionEntryRoleRepository,
            new EntryTypeRepository,
            new EntryTypeTranslationRepository,
            new ActionRepository,
            new ActionTranslationRepository,
            new ActionEntryTypeRoleRepository,
            new FieldRepository,
            new FieldAttributeRepository,
            new FieldValueRepository,
            new SectionRepository,
            new SectionTranslationRepository,
            new LocaleRepository
        );
        $validateEmptyKeysFromForm = new ValidateEmptyKeysFromForm;
        $validateDuplicateKeyFromForm = new ValidateDuplicateKeyFromForm;
        $getEntryTypeByName = new GetEntryTypeByName(
            new EntryTypeRepository,
            new EntryTypeTranslationRepository,
            new ActionRepository,
            new ActionTranslationRepository,
            new ActionEntryTypeRoleRepository,
            new FieldRepository,
            new LocaleRepository,
            new SectionRepository,
            new SectionTranslationRepository,
        );

        $entryType = $getEntryTypeByName->handle(static::$entry_type, true, false);

        if ($form === null) {
            $form = $entryType->form;
        }

        // check form for empty keys
        $emptyKeysResponse = $validateEmptyKeysFromForm->handle($form);
        if (!empty($emptyKeysResponse)) {
            throw new \Exception(__('cyvian.errors.empty_key'));
        }

        // check form for duplicate keys
        $duplicateKeysResponse = $validateDuplicateKeyFromForm->handle($form);
        if (!empty($duplicateKeysResponse)) {
            throw new \Exception(__('cyvian.errors.duplicate_key'));
        }

        $form = $mergeValuesArrayIntoForm->handle($values, $form);

        if (empty($actions)) {
            $actions = $entryType->actions;
        }

        $superAdminUser = User::where('super_admin', true)->get()->first();
        $superAdminUserId = 2;
        if ($superAdminUser) {
            $superAdminUserId = $superAdminUser->id;
        }

        $entry = new Entry(
            0,
            $entryType->id,
            $superAdminUserId,
            $superAdminUserId,
            $actions,
            $form
        );

        $handlerResponse = $createEntry->handle($entry);
        if (!$handlerResponse->isSuccessful) {
            throw new \Exception(__('cyvian.exceptions.entry_not_created'));
        }
        $entry = $handlerResponse->data;

        return $entry;
    }

    public function update(array $values, array $additionalFields = [], $additionalTabs = [])
    {
        $entryType = EntryType::where('name', static::$entry_type)->get()->first();
        $fields = array_merge($entryType->fields->toArray(), $additionalFields);
        $errors = EntryHelper::validateValues($values, $fields);
        if (!empty($errors)) {
            $errorsString = '';
            foreach ($errors as $key => $error) {
                $errorsString .= $key . ',';
            };
            substr($errorsString, 0, -1);
            throw new \Exception(__('cyvian.exceptions.validation') . $errorsString);
        }

        $entry = Entry::find($this->id);
        $entry->updated_by = Auth::id();
        $entry->save();
        $entry->tabs()->delete();
        $entry->fields()->delete();

        $additionalTabs = TabHelper::createTabsFromForm($additionalTabs, $entry->id, Entry::class);
        $additionalFields = FieldHelper::ajustTabsInFields(array_merge($entryType->tabs->toArray(), $additionalTabs), $additionalFields);
        FieldValue::where('entry_id', $entry->id)->delete();
        $additionalFields = FieldHelper::createFields($additionalFields, $entry->id, Entry::class);
        FieldHelper::createValues($values, collect(array_merge($entryType->fields->all(), $additionalFields)), $entry, null);
        $class = self::getChildClassPath();

        return $class::find($entry->id);
    }

    public function delete(): bool
    {
        // todo better
        $entry = Entry::find($this->id);
        $entry->delete();

        return true;
    }
}
