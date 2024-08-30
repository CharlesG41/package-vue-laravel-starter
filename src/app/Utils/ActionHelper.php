<?php

namespace Cyvian\Src\App\Utils;

use Cyvian\Src\App\Models\Cyvian\Action;
use Cyvian\Src\App\Models\Cyvian\EntryType;
use Cyvian\Src\App\Models\Cyvian\Locale;
use Cyvian\Src\App\Models\Role;
use Cyvian\Src\App\Models\Cyvian\Translations\ActionTranslation;
use Illuminate\Contracts\Routing\UrlGenerator;
use Illuminate\Support\Facades\DB;

class ActionHelper
{
    static public function createBaseActions(EntryType $entryType, array $actionsToCreate = [], bool $rolesByEntry = false, array $roles = []): array
    {
        $actions = [];
        $locales = config('locales.locales');

        if (empty($actionsToCreate) || in_array('list', $actionsToCreate)) {
            $action = Action::create([
                'name' => 'list',
                'position' => 'general',
                'roles_by_entry' => false,
                'entry_type_id' => $entryType->id,
            ]);
        }

        if (empty($actionsToCreate) || in_array('modify_fields', $actionsToCreate)) {
            $action = Action::create([
                'name' => 'modify_fields',
                'position' => 'general',
                'roles_by_entry' => false,
                'entry_type_id' => $entryType->id,
            ]);
        }

        if (empty($actionsToCreate) || in_array('edit', $actionsToCreate)) {
            $action = Action::create([
                'name' => 'edit',
                'position' => 'row',
                'action_type' => Action::ACTION_TYPE_VIEW,
                'url' => '/{entry_type}/edit/{id}',
                'roles_by_entry' => $rolesByEntry,
                'entry_type_id' => $entryType->id,
            ]);
            $actions[] = $action;
            $labels = LocaleHelper::mapTranslation('cyvian.actions.labels.edit');
            foreach ($locales as $locale) {
                ActionTranslation::create([
                    'label' => $labels[$locale->code],
                    'parent_id' => $action->id,
                    'locale_id' => $locale->id
                ]);
            }
            if (!$rolesByEntry) {
                foreach ($roles as $role) {
                    DB::table('action_entry_type_role')->insert(['action_id' => $action->id, 'entry_type_id' => $entryType->id, 'role_id' => $role]);
                }
            }
        }

        if (empty($actionsToCreate) || in_array('delete', $actionsToCreate)) {
            $action = Action::create([
                'name' => 'delete',
                'position' => 'row',
                'action_type' => Action::ACTION_TYPE_EXECUTE,
                'url' => '/{entry_type}/actions/{name}',
                'entry_type_id' => $entryType->id,
                'roles_by_entry' => $rolesByEntry,
            ]);
            $actions[] = $action;
            $labels = LocaleHelper::mapTranslation('cyvian.actions.labels.delete');
            $messages = LocaleHelper::mapTranslation('cyvian.actions.messages.delete');
            $actionLabels = LocaleHelper::mapTranslation('cyvian.actions.action_labels.delete');
            foreach ($locales as $locale) {
                ActionTranslation::create([
                    'label' => $labels[$locale->code],
                    'message' => $messages[$locale->code],
                    'action_label' => $actionLabels[$locale->code],
                    'parent_id' => $action->id,
                    'locale_id' => $locale->id
                ]);
            }
            if (!$rolesByEntry) {
                foreach ($roles as $role) {
                    DB::table('action_entry_type_role')->insert(['action_id' => $action->id, 'entry_type_id' => $entryType->id, 'role_id' => $role]);
                }
            }
        }

        if (empty($actionsToCreate) || in_array('create', $actionsToCreate)) {
            $action = Action::create([
                'name' => 'create',
                'position' => 'top',
                'action_type' => Action::ACTION_TYPE_VIEW,
                'url' => '/{entry_type}/create',
                'entry_type_id' => $entryType->id,
                'roles_by_entry' => false,
            ]);
            $actions[] = $action;
            $labels = LocaleHelper::mapTranslation('cyvian.actions.labels.create');
            foreach ($locales as $locale) {
                ActionTranslation::create([
                    'label' => $labels[$locale->code],
                    'parent_id' => $action->id,
                    'locale_id' => $locale->id
                ]);
            }
            if (!$rolesByEntry) {
                foreach ($roles as $role) {
                    DB::table('action_entry_type_role')->insert(['action_id' => $action->id, 'entry_type_id' => $entryType->id, 'role_id' => $role]);
                }
            }
        }

        if (empty($actionsToCreate) || in_array('mass_delete', $actionsToCreate)) {
            $action = Action::create([
                'name' => 'mass_delete',
                'icon' => 'trash',
                'position' => 'top',
                'action_type' => Action::ACTION_TYPE_EXECUTE,
                'url' => '/{entry_type}/actions/{name}',
                'entry_type_id' => $entryType->id,
                'roles_by_entry' => false,
            ]);
            $actions[] = $action;
            $labels = LocaleHelper::mapTranslation('cyvian.actions.labels.mass_delete');
            $messages = LocaleHelper::mapTranslation('cyvian.actions.messages.mass_delete');
            $actionLabels = LocaleHelper::mapTranslation('cyvian.actions.action_labels.mass_delete');
            foreach ($locales as $locale) {
                ActionTranslation::create([
                    'label' => $labels[$locale->code],
                    'message' => $messages[$locale->code],
                    'action_label' => $actionLabels[$locale->code],
                    'parent_id' => $action->id,
                    'locale_id' => $locale->id
                ]);
            }
            if (!$rolesByEntry) {
                foreach ($roles as $role) {
                    DB::table('action_entry_type_role')->insert(['action_id' => $action->id, 'entry_type_id' => $entryType->id, 'role_id' => $role]);
                }
            }
        }

        if (empty($actionsToCreate) || in_array('duplicate', $actionsToCreate)) {
            $action = Action::create([
                'name' => 'duplicate',
                'icon' => 'copy',
                'position' => 'row',
                'action_type' => Action::ACTION_TYPE_EXECUTE,
                'url' => '/{entry_type}/actions/{name}',
                'entry_type_id' => $entryType->id,
                'roles_by_entry' => $rolesByEntry,
            ]);
            $actions[] = $action;
            $labels = LocaleHelper::mapTranslation('cyvian.actions.labels.duplicate');
            $messages = LocaleHelper::mapTranslation('cyvian.actions.messages.duplicate');
            $actionLabels = LocaleHelper::mapTranslation('cyvian.actions.action_labels.duplicate');
            foreach ($locales as $locale) {
                ActionTranslation::create([
                    'label' => $labels[$locale->code],
                    'message' => $messages[$locale->code],
                    'action_label' => $actionLabels[$locale->code],
                    'parent_id' => $action->id,
                    'locale_id' => $locale->id
                ]);
            }
            if (!$rolesByEntry) {
                foreach ($roles as $role) {
                    DB::table('action_entry_type_role')->insert(['action_id' => $action->id, 'entry_type_id' => $entryType->id, 'role_id' => $role]);
                }
            }
        }

        return $actions;
    }

    static public function createListAction(EntryType $entryType, $roles)
    {
        $action = Action::create([
            'name' => 'list',
            'position' => 'general',
            'roles_by_entry' => false,
            'entry_type_id' => $entryType->id,
        ]);
        foreach ($roles as $role) {
            DB::table('action_entry_type_role')->insert([
                'action_id' => $action->id,
                'entry_type_id' => $entryType->id,
                'role_id' => is_numeric($role) ? $role : $role->id
            ]);
        }
    }

    static public function createModifyFieldsAction(EntryType $entryType, $roles)
    {
        $action = Action::create([
            'name' => 'modify_fields',
            'position' => 'general',
            'roles_by_entry' => false,
            'entry_type_id' => $entryType->id,
        ]);
        foreach ($roles as $role) {
            DB::table('action_entry_type_role')->insert([
                'action_id' => $action->id,
                'entry_type_id' => $entryType->id,
                'role_id' => is_numeric($role) ? $role : $role->id
            ]);
        }
    }

    static public function createCreateAction(EntryType $entryType, $roles)
    {
        $action = Action::create([
            'name' => 'create',
            'icon' => 'add',
            'position' => 'top',
            'action_type' => Action::ACTION_TYPE_VIEW,
            'url' => '/{entry_type}/create',
            'entry_type_id' => $entryType->id,
            'confirm' => false,
            'roles_by_entry' => false,
        ]);
        $labels = LocaleHelper::mapTranslation('cyvian.actions.labels.create');
        foreach (config('locales.locales') as $locale) {
            ActionTranslation::create([
                'label' => $labels[$locale->code],
                'parent_id' => $action->id,
                'locale_id' => $locale->id,
            ]);
        }
        foreach ($roles as $role) {
            DB::table('action_entry_type_role')->insert([
                'action_id' => $action->id,
                'entry_type_id' => $entryType->id,
                'role_id' => is_numeric($role) ? $role : $role->id
            ]);
        }
    }

    static public function createAction(array $action, EntryType $entryType, string $position): void
    {
        if (array_key_exists('id', $action)) {
            self::updateAction($action, $entryType, $position);
        } else {
            $locales = config('locales.locales');
            $parent = Action::create([
                'name' => $action['name'],
                'position' => $position,
                'action_type' => $action['action_type'],
                'url' => $action['url'],
                'roles_by_entry' => $action['roles_by_entry'] ?? false,
                'entry_type_id' => $entryType->id,
            ]);

            foreach ($locales as $locale) {
                ActionTranslation::create([
                    'label' => array_key_exists('label', $action) ? $action['label'][$locale->code] : null,
                    'message' => array_key_exists('message', $action) ? $action['message'][$locale->code] : null,
                    'action_label' => array_key_exists('action_label', $action) ? $action['action_label'][$locale->code] : null,
                    'parent_id' => $parent->id,
                    'locale_id' => $locale->id,
                ]);
            }

            FieldHelper::createFields($action['fields'] ?? [], $parent->id, Action::class);

            // if action has role_by_entry, we need to add a field to the base_fields of the entry_type and populate all of the previous entry
            if ($parent->roles_by_entry) {
                foreach ($action['labels'] as $lang => $label) {
                    $labels[$lang] = __('cyvian.actions.label', ['action' => $label]);
                }
                FieldHelper::createFields([
                    [
                        'name' => $labels,
                        'key' => '__action_' . $action['name'],
                        'position' => 'right',
                        'locked' => true,
                        'type' => 'many_entries',
                        'entry_types' => [EntryType::where('name', 'role')->get()->first()->id],
                        'roles_on_edit_or_hide' => [Role::where('slug', 'admin')->first()->id],
                        'default' => $action['default_roles'],
                        'action_id' => $parent->id,
                    ]
                ], $entryType->id, EntryType::class);
                // retroactively populate all entries with the default roles
                $rows = [];
                $entryType->entries->each(function ($entry) use ($action, $parent, &$rows) {
                    foreach ($action['default_roles'] as $roleId) {
                        $rows[] = ['action_id' => $parent->id, 'entry_id' => $entry->id, 'role_id' => $roleId];
                    }
                });
                DB::table('action_entry_role')->insert($rows);
            } else {
                $rows = [];
                if (empty($action['roles'])) {
                    $roleIds = Role::all()->pluck('id');
                    foreach ($roleIds as $roleId) {
                        $rows[] = ['action_id' => $parent->id, 'entry_type_id' => $entryType->id, 'role_id' => $roleId];
                    }
                    DB::table('action_entry_type_role')->insert($rows);
                } else {
                    foreach ($action['roles'] as $roleId) {
                        $rows[] = ['action_id' => $parent->id, 'entry_type_id' => $entryType->id, 'role_id' => $roleId];
                    }
                    DB::table('action_entry_type_role')->insert($rows);
                }
            }

            FieldHelper::createFields($action['fields'] ?? [], $parent->id, Action::class);
        }
    }

    static public function createActions(array $actions, EntryType $entryType, string $position): void
    {
        foreach ($actions as $action) {
            self::createAction($action, $entryType, $position);
        }
    }

    static public function createGeneralActions(array $actions, EntryType $entryType): void
    {
        // create list action
        $action = Action::create([
            'name' => 'list',
            'position' => 'general',
            'roles_by_entry' => false,
            'entry_type_id' => $entryType->id,
        ]);
        $rows = [];
        foreach ($actions['list_roles'] as $roleId) {
            $rows[] = ['action_id' => $action->id, 'entry_type_id' => $entryType->id, 'role_id' => $roleId];
        }
        DB::table('action_entry_type_role')->insert($rows);

        // create modify_fields action
        $action = Action::create([
            'name' => 'modify_fields',
            'position' => 'general',
            'roles_by_entry' => false,
            'entry_type_id' => $entryType->id,
        ]);
        $rows = [];
        foreach ($actions['modify_fields_roles'] as $roleId) {
            $rows[] = ['action_id' => $action->id, 'entry_type_id' => $entryType->id, 'role_id' => $roleId];
        }
        DB::table('action_entry_type_role')->insert($rows);
    }

    static public function updateAction(array $action, EntryType $entryType, $position): void
    {
        $locales = config('locales.locales');
        $actionObject = Action::find($action['id']);
        $rolesByEntryIsModified = $actionObject->roles_by_entry != $action['roles_by_entry'];
        $actionObject->name = $action['name'];
        $actionObject->position = $action['position'];
        $actionObject->action_type = $action['action_type'];
        $actionObject->url = $action['url'];
        $actionObject->roles_by_entry = array_key_exists('roles_by_entry', $action) ? $action['roles_by_entry'] : false;
        $actionObject->save();

        $actionObject->translation()->delete();

        foreach ($locales as $locale) {
            ActionTranslation::create([
                'label' => array_key_exists('label', $action) ? $action['label'][$locale->code] : null,
                'message' => array_key_exists('message', $action) ? $action['message'][$locale->code] : null,
                'action_label' => array_key_exists('action_label', $action) ? $action['action_label'][$locale->code] : null,
                'parent_id' => $actionObject->id,
                'locale_id' => $locale->id,
            ]);
        }

        // delete all fields and create new ones
        $actionObject->fields()->delete();

        FieldHelper::createFields($action['fields'] ?? [], $actionObject->id, Action::class);

        if ($actionObject->roles_by_entry) {
            foreach ($action['labels'] as $lang => $label) {
                $labels[$lang] = __('cyvian.actions.label', ['action' => $label]);
            }
            FieldHelper::createFields([
                [
                    'name' => $labels,
                    'key' => '__action_' . $action['name'],
                    'position' => 'right',
                    'locked' => true,
                    'type' => 'many_entries',
                    'entry_types' => [EntryType::where('name', 'role')->get()->first()->id],
                    'roles_on_edit_or_hide' => [Role::where('super_admin', true)->first()->id],
                    'default' => $action['default_roles']
                ]
            ], $entryType->id, EntryType::class);

            if ($rolesByEntryIsModified) {
                // retroactively populate all entries with the default roles
                $rows = [];
                $entryType->entries->each(function ($entry) use ($action, $actionObject, &$rows) {
                    foreach ($action['default_roles'] as $roleId) {
                        $rows[] = ['action_id' => $actionObject->id, 'entry_id' => $entry->id, 'role_id' => $roleId];
                    }
                });
                DB::table('action_entry_role')->insert($rows);
            }
        } else {
            if ($rolesByEntryIsModified) {
                // delete all action by entry role, and
                DB::table('action_entry_role')->where('action_id', $actionObject->id)->delete();

                $rows = [];
                foreach ($action['roles'] as $roleId) {
                    $rows[] = ['action_id' => $actionObject->id, 'entry_type_id' => $entryType->id, 'role_id' => $roleId];
                }
                DB::table('action_entry_type_role')->insert($rows);
            }
        }
    }

    static public function updateGeneralActions(array $actions): void
    {
    }
}
