<?php

namespace Cyvian\Src\database\seeders\refactored;

use Cvyian\Src\App\Handlers\Action\CreateBaseActions;
use Cyvian\Src\app\Classes\Entry;
use Cyvian\Src\app\Classes\EntryType;
use Cyvian\Src\app\Classes\Fields\Boolean;
use Cyvian\Src\app\Classes\Fields\Classes\Condition;
use Cyvian\Src\app\Classes\Fields\Classes\FieldPermissions;
use Cyvian\Src\app\Classes\Fields\Fields;
use Cyvian\Src\app\Classes\Fields\Group;
use Cyvian\Src\app\Classes\Fields\ManyEntries;
use Cyvian\Src\app\Classes\Fields\Repeater;
use Cyvian\Src\app\Classes\Fields\Sections;
use Cyvian\Src\app\Classes\Fields\SelectOne;
use Cyvian\Src\app\Classes\Fields\Text;
use Cyvian\Src\app\Classes\Fields\Wysiwyg;
use Cyvian\Src\app\Classes\Form;
use Cyvian\Src\app\Classes\Locale;
use Cyvian\Src\app\Classes\Section;
use Cyvian\Src\app\Classes\Translations\EntryTypeTranslation;
use Cyvian\Src\app\Classes\Translations\SectionTranslation;
use Cyvian\Src\app\Handlers\EntryType\CreateEntryType;
use Cyvian\Src\App\Handlers\Locale\GetLocalesByType;
use Cyvian\Src\app\Handlers\MenuSection\GetMenuSectionOptions;
use Cyvian\Src\app\Repositories\ActionEntryTypeRoleRepository;
use Cyvian\Src\app\Repositories\ActionRepository;
use Cyvian\Src\app\Repositories\ActionTranslationRepository;
use Cyvian\Src\app\Repositories\EntryTypeRepository;
use Cyvian\Src\app\Repositories\EntryTypeTranslationRepository;
use Cyvian\Src\app\Repositories\FieldAttributeRepository;
use Cyvian\Src\app\Repositories\FieldRepository;
use Cyvian\Src\app\Repositories\FieldValueRepository;
use Cyvian\Src\app\Repositories\LocaleRepository;
use Cyvian\Src\app\Repositories\MenuSectionRepository;
use Cyvian\Src\app\Repositories\MenuSectionTranslationRepository;
use Cyvian\Src\app\Repositories\SectionRepository;
use Cyvian\Src\app\Repositories\SectionTranslationRepository;
use Cyvian\Src\app\Utils\Localisation;
use Illuminate\Database\Seeder;

class EntryTypesSeeder extends Seeder
{
    public function run()
    {
        $entryTypeRepository = new EntryTypeRepository;
        $entryTypeTranslationRepository = new EntryTypeTranslationRepository;
        $actionRepository = new ActionRepository;
        $actionTranslationRepository = new ActionTranslationRepository;
        $actionEntryTypeRoleRepository = new ActionEntryTypeRoleRepository;
        $fieldRepository = new FieldRepository;
        $fieldAttributeRepository = new FieldAttributeRepository;
        $fieldValueRepository = new FieldValueRepository;
        $sectionRepository = new SectionRepository;
        $sectionTranslationRepository = new SectionTranslationRepository;
        $localeRepository = new LocaleRepository;

        $createEntryType = new CreateEntryType(
            $entryTypeRepository,
            $entryTypeTranslationRepository,
            $actionRepository,
            $actionTranslationRepository,
            $actionEntryTypeRoleRepository,
            $fieldRepository,
            $fieldAttributeRepository,
            $fieldValueRepository,
            $sectionRepository,
            $sectionTranslationRepository,
            $localeRepository
        );
        $getLocalesByType = new GetLocalesByType(
            $localeRepository
        );
        $createBaseActions = new CreateBaseActions(
            $actionRepository,
            $actionTranslationRepository,
            $actionEntryTypeRoleRepository,
            $fieldRepository,
            $fieldAttributeRepository,
            $localeRepository
        );
        $getMenuSectionOptions = new GetMenuSectionOptions(
            new MenuSectionRepository,
            new MenuSectionTranslationRepository,
            new LocaleRepository
        );

        $localesCms = $getLocalesByType->handle(Locale::IS_CMS);

        $menuSectionOptions = $getMenuSectionOptions->handle();

        $entryType = new EntryType(
            'entry_type',
            'model',
            2,
            new EntryTypeTranslation(Localisation::mapTranslation('cyvian.entry_types.entry_type.singular_name', [], $localesCms), Localisation::mapTranslation('cyvian.entry_types.entry_type.plural_name', [], $localesCms)),
            new Form(
                [
                    new Section(
                        new SectionTranslation(
                            Localisation::mapEmpty($localesCms),
                        ),
                        null,
                        Section::POSITION_LEFT,
                        [
                            new Text(
                                'singular_name',
                                Localisation::mapTranslation('cyvian.fields.singular_name', [], $localesCms),
                                Localisation::mapEmpty($localesCms),
                                true,
                                6,
                                true,
                                null,
                                Localisation::mapEmpty($localesCms),
                                1,
                                null,
                                [],
                                new FieldPermissions(),
                                true
                            ),
                            new Text(
                                'plural_name',
                                Localisation::mapTranslation('cyvian.fields.plural_name', [], $localesCms),
                                Localisation::mapEmpty($localesCms),
                                true,
                                6,
                                false,
                                null,
                                Localisation::mapEmpty($localesCms),
                                1,
                                null,
                                [],
                                new FieldPermissions(),
                                true
                            ),
                            new Text(
                                'name',
                                Localisation::mapTranslation('cyvian.fields.name', [], $localesCms),
                                Localisation::mapEmpty($localesCms),
                                false,
                                4,
                                false,
                                null,
                                Localisation::mapEmpty($localesCms),
                                1,
                                null,
                                [],
                                new FieldPermissions(
                                    false,
                                    false,
                                    true,
                                    [],
                                    [],
                                    []
                                ),
                                true,
                            ),
                            new SelectOne(
                                'type',
                                Localisation::mapTranslation('cyvian.fields.type', [], $localesCms),
                                Localisation::mapEmpty($localesCms),
                                false,
                                true,
                                4,
                                false,
                                true,
                                [
                                    'model' => Localisation::mapTranslation('cyvian.fields.model', [], $localesCms),
                                    'setting' => Localisation::mapTranslation('cyvian.fields.setting', [], $localesCms),
                                ],
                                [],
                                new FieldPermissions(
                                    false,
                                    false,
                                    true,
                                    [],
                                    [],
                                    []
                                ),
                                true,
                            ),
                            new SelectOne(
                                'menu_section',
                                Localisation::mapTranslation('cyvian.fields.menu', [], $localesCms),
                                Localisation::mapEmpty($localesCms),
                                false,
                                true,
                                4,
                                false,
                                false,
                                $menuSectionOptions,
                                [],
                                new FieldPermissions(),
                                true,
                            ),
                        ],
                    ),
                    new Section(
                        new SectionTranslation(
                            Localisation::mapTranslation('cyvian.sections.labels.base_form', [], $localesCms)
                        ),
                        null,
                        Section::POSITION_LEFT,
                        [
                            new Sections(
                                'sections',
                                Localisation::mapTranslation('cyvian.fields.sections', [], $localesCms),
                                Localisation::mapEmpty($localesCms),
                                false,
                                1,
                                null,
                                [],
                                new FieldPermissions(),
                                true
                            ),
                        ]
                    ),
                    new Section(
                        new SectionTranslation(
                            Localisation::mapTranslation('cyvian.sections.labels.actions', [], $localesCms)
                        ),
                        'actions',
                        Section::POSITION_LEFT,
                        [
                            new Group(
                                'general_actions',
                                Localisation::mapTranslation('cyvian.fields.general_actions', [], $localesCms),
                                Localisation::mapEmpty($localesCms),
                                [
                                    new ManyEntries(
                                        'list_roles',
                                        Localisation::mapTranslation('cyvian.fields.list_roles', [], $localesCms),
                                        Localisation::mapTranslation('cyvian.descriptions.if_empty_all_roles_have_access', [], $localesCms),
                                        false,
                                        6,
                                        false,
                                        false,
                                        [\Cyvian\Src\App\Models\Cyvian\EntryType::where('name', 'role')->get()->first()->id],
                                        null,
                                        null,
                                        [],
                                        new FieldPermissions(),
                                        true
                                    ),
                                    new ManyEntries(
                                        'modify_fields_roles',
                                        Localisation::mapTranslation('cyvian.fields.modify_fields_roles', [], $localesCms),
                                        Localisation::mapTranslation('cyvian.descriptions.if_empty_all_roles_have_access', [], $localesCms),
                                        false,
                                        6,
                                        false,
                                        false,
                                        [\Cyvian\Src\App\Models\Cyvian\EntryType::where('name', 'role')->get()->first()->id],
                                        null,
                                        null,
                                        [],
                                        new FieldPermissions(),
                                        true
                                    )
                                ],
                                [],
                                new FieldPermissions(),
                                true
                            ),
                            new Repeater(
                                'top_actions',
                                Localisation::mapTranslation('cyvian.fields.top_actions', [], $localesCms),
                                Localisation::mapEmpty($localesCms),
                                false,
                                null,
                                null,
                                [
                                    new Text(
                                        'label',
                                        Localisation::mapTranslation('cyvian.fields.label', [], $localesCms),
                                        Localisation::mapEmpty($localesCms),
                                        true,
                                        4,
                                        false,
                                        null,
                                        Localisation::mapEmpty($localesCms),
                                        1,
                                        null,
                                        [],
                                        new FieldPermissions(),
                                        true
                                    ),
                                    new Text(
                                        'name',
                                        Localisation::mapTranslation('cyvian.fields.name', [], $localesCms),
                                        Localisation::mapTranslation('cyvian.descriptions.name', [], $localesCms),
                                        false,
                                        4,
                                        false,
                                        '[^\s-]',
                                        Localisation::mapTranslation('cyvian.regex_messages.no_whitespace', [], $localesCms),
                                        1,
                                        null,
                                        [],
                                        new FieldPermissions(),
                                        true
                                    ),
                                    new Text(
                                        'url',
                                        Localisation::mapTranslation('cyvian.fields.url', [], $localesCms),
                                        Localisation::mapTranslation('cyvian.descriptions.url', [], $localesCms),
                                        false,
                                        4,
                                        false,
                                        null,
                                        Localisation::mapEmpty($localesCms),
                                        1,
                                        null,
                                        [],
                                        new FieldPermissions(),
                                        true
                                    ),
                                    new ManyEntries(
                                        'role_ids',
                                        Localisation::mapTranslation('cyvian.fields.roles', [], $localesCms),
                                        Localisation::mapTranslation('cyvian.descriptions.roles', [], $localesCms),
                                        false,
                                        12,
                                        false,
                                        false,
                                        [\Cyvian\Src\App\Models\Cyvian\EntryType::where('name', 'role')->get()->first()->id],
                                        null,
                                        null,
                                        [],
                                        new FieldPermissions(),
                                        true
                                    ),
                                    new SelectOne(
                                        'action_type',
                                        Localisation::mapTranslation('cyvian.fields.action_type', [], $localesCms),
                                        Localisation::mapTranslation('cyvian.descriptions.action_type', [], $localesCms),
                                        false,
                                        true,
                                        4,
                                        false,
                                        false,
                                        [
                                            'view' => Localisation::mapTranslation('cyvian.fields.options.view', [], $localesCms),
                                            'download' => Localisation::mapTranslation('cyvian.fields.options.download', [], $localesCms),
                                            'execute' => Localisation::mapTranslation('cyvian.fields.options.execute', [], $localesCms),
                                        ],
                                        [],
                                        new FieldPermissions(),
                                        true
                                    ),
                                    new Wysiwyg(
                                        'message',
                                        Localisation::mapTranslation('cyvian.fields.message', [], $localesCms),
                                        Localisation::mapTranslation('cyvian.descriptions.message', [], $localesCms),
                                        true,
                                        8,
                                        null,
                                        null,
                                        [
                                            new Condition(
                                                'action_type',
                                                '!=',
                                                'view'
                                            )
                                        ],
                                        new FieldPermissions(),
                                        false,
                                    ),
                                    new Text(
                                        'action_label',
                                        Localisation::mapTranslation('cyvian.fields.action_label', [], $localesCms),
                                        Localisation::mapTranslation('cyvian.descriptions.action_label', [], $localesCms),
                                        true,
                                        12,
                                        true,
                                        null,
                                        Localisation::mapEmpty($localesCms),
                                        null,
                                        null,
                                        [
                                            new Condition(
                                                'action_type',
                                                '!=',
                                                'view'
                                            )
                                        ],
                                        new FieldPermissions(),
                                        true
                                    ),
                                    new Sections(
                                        'sections',
                                        Localisation::mapTranslation('cyvian.fields.sections', [], $localesCms),
                                        Localisation::mapEmpty($localesCms),
                                        false,
                                        null,
                                        null,
                                        [
                                            new Condition(
                                                'action_type',
                                                '!=',
                                                'view'
                                            )
                                        ],
                                        new FieldPermissions(),
                                        true
                                    ),
                                ],
                                [
                                    new Condition(
                                        'type',
                                        '!=',
                                        EntryType::TYPE_SETTING
                                    )
                                ],
                                new FieldPermissions(),
                                true
                            ),
                            new Repeater(
                                'row_actions',
                                Localisation::mapTranslation('cyvian.fields.row_actions', [], $localesCms),
                                Localisation::mapEmpty($localesCms),
                                false,
                                null,
                                null,
                                [
                                    new Text(
                                        'label',
                                        Localisation::mapTranslation('cyvian.fields.label', [], $localesCms),
                                        Localisation::mapEmpty($localesCms),
                                        true,
                                        4,
                                        false,
                                        null,
                                        Localisation::mapEmpty($localesCms),
                                        1,
                                        null,
                                        [],
                                        new FieldPermissions(),
                                        true
                                    ),
                                    new Text(
                                        'name',
                                        Localisation::mapTranslation('cyvian.fields.name', [], $localesCms),
                                        Localisation::mapTranslation('cyvian.descriptions.name', [], $localesCms),
                                        false,
                                        4,
                                        false,
                                        '[^\s-]',
                                        Localisation::mapTranslation('cyvian.regex_messages.no_whitespace', [], $localesCms),
                                        1,
                                        null,
                                        [],
                                        new FieldPermissions(),
                                        true
                                    ),
                                    new Text(
                                        'url',
                                        Localisation::mapTranslation('cyvian.fields.url', [], $localesCms),
                                        Localisation::mapTranslation('cyvian.descriptions.url', [], $localesCms),
                                        false,
                                        4,
                                        false,
                                        null,
                                        Localisation::mapEmpty($localesCms),
                                        1,
                                        null,
                                        [],
                                        new FieldPermissions(),
                                        true
                                    ),
                                    new Boolean(
                                        'roles_by_entry',
                                        Localisation::mapTranslation('cyvian.fields.roles_by_entry', [], $localesCms),
                                        Localisation::mapTranslation('cyvian.descriptions.roles_by_entry', [], $localesCms),
                                        false,
                                        5,
                                        false,
                                        false,
                                        [],
                                        new FieldPermissions(),
                                        true
                                    ),
                                    new ManyEntries(
                                        'default_roles',
                                        Localisation::mapTranslation('cyvian.fields.default_roles', [], $localesCms),
                                        Localisation::mapTranslation('cyvian.descriptions.default_roles', [], $localesCms),
                                        false,
                                        7,
                                        false,
                                        false,
                                        [\Cyvian\Src\App\Models\Cyvian\EntryType::where('name', 'role')->get()->first()->id],
                                        null,
                                        null,
                                        [
                                            new Condition(
                                                'roles_by_entry',
                                                '=',
                                                true
                                            )
                                        ],
                                        new FieldPermissions(),
                                        true
                                    ),
                                    new ManyEntries(
                                        'role_ids',
                                        Localisation::mapTranslation('cyvian.fields.roles', [], $localesCms),
                                        Localisation::mapTranslation('cyvian.descriptions.roles', [], $localesCms),
                                        false,
                                        12,
                                        false,
                                        false,
                                        [\Cyvian\Src\App\Models\Cyvian\EntryType::where('name', 'role')->get()->first()->id],
                                        null,
                                        null,
                                        [],
                                        new FieldPermissions(),
                                        true
                                    ),
                                    new SelectOne(
                                        'action_type',
                                        Localisation::mapTranslation('cyvian.fields.action_type', [], $localesCms),
                                        Localisation::mapTranslation('cyvian.descriptions.action_type', [], $localesCms),
                                        false,
                                        true,
                                        4,
                                        false,
                                        false,
                                        [
                                            'view' => Localisation::mapTranslation('cyvian.fields.options.view', [], $localesCms),
                                            'download' => Localisation::mapTranslation('cyvian.fields.options.download', [], $localesCms),
                                            'execute' => Localisation::mapTranslation('cyvian.fields.options.execute', [], $localesCms),
                                        ],
                                        [],
                                        new FieldPermissions(),
                                        true
                                    ),
                                    new Wysiwyg(
                                        'message',
                                        Localisation::mapTranslation('cyvian.fields.message', [], $localesCms),
                                        Localisation::mapTranslation('cyvian.descriptions.message', [], $localesCms),
                                        true,
                                        8,
                                        null,
                                        null,
                                        [
                                            new Condition(
                                                'action_type',
                                                '!=',
                                                'view'
                                            )
                                        ],
                                        new FieldPermissions(),
                                        false,
                                    ),
                                    new Text(
                                        'action_label',
                                        Localisation::mapTranslation('cyvian.fields.action_label', [], $localesCms),
                                        Localisation::mapTranslation('cyvian.descriptions.action_label', [], $localesCms),
                                        true,
                                        12,
                                        false,
                                        null,
                                        Localisation::mapEmpty($localesCms),
                                        null,
                                        null,
                                        [
                                            new Condition(
                                                'action_type',
                                                '!=',
                                                'view'
                                            )
                                        ],
                                        new FieldPermissions(),
                                        true
                                    ),
                                    new Sections(
                                        'sections',
                                        Localisation::mapTranslation('cyvian.fields.sections', [], $localesCms),
                                        Localisation::mapEmpty($localesCms),
                                        false,
                                        null,
                                        null,
                                        [
                                            new Condition(
                                                'action_type',
                                                '!=',
                                                'view'
                                            )
                                        ],
                                        new FieldPermissions(),
                                        true
                                    ),
                                ],
                                [
                                    new Condition(
                                        'type',
                                        '!=',
                                        EntryType::TYPE_SETTING
                                    )
                                ],
                                new FieldPermissions(),
                                true
                            )
                        ]
                    )
                ]
            ),
            []
        );
        $entryType = $createEntryType->handle($entryType);

        $createBaseActions->handle(
            true,
            true,
            true,
            true,
            true,
            false,
            false,
            $entryType->id,
            [1]
        );
    }
}
