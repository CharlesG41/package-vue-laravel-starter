<?php

namespace Cyvian\Src\database\seeders\refactored;

use Cvyian\Src\App\Handlers\Action\CreateBaseActions;
use Cyvian\Src\app\Classes\EntryType;
use Cyvian\Src\app\Classes\Fields\Boolean;
use Cyvian\Src\app\Classes\Fields\Classes\FieldPermissions;
use Cyvian\Src\app\Classes\Fields\Classes\FlexibleSection;
use Cyvian\Src\app\Classes\Fields\Date;
use Cyvian\Src\app\Classes\Fields\Datetime;
use Cyvian\Src\app\Classes\Fields\Email;
use Cyvian\Src\app\Classes\Fields\File;
use Cyvian\Src\app\Classes\Fields\Flexible;
use Cyvian\Src\app\Classes\Fields\Group;
use Cyvian\Src\app\Classes\Fields\Image;
use Cyvian\Src\app\Classes\Fields\ManyEntries;
use Cyvian\Src\app\Classes\Fields\Number;
use Cyvian\Src\app\Classes\Fields\OneEntry;
use Cyvian\Src\app\Classes\Fields\Password;
use Cyvian\Src\app\Classes\Fields\Repeater;
use Cyvian\Src\app\Classes\Fields\Sections;
use Cyvian\Src\app\Classes\Fields\SelectMany;
use Cyvian\Src\app\Classes\Fields\SelectOne;
use Cyvian\Src\app\Classes\Fields\Slug;
use Cyvian\Src\app\Classes\Fields\Text;
use Cyvian\Src\app\Classes\Fields\Textarea;
use Cyvian\Src\app\Classes\Fields\Url;
use Cyvian\Src\app\Classes\Fields\Wysiwyg;
use Cyvian\Src\app\Classes\Form;
use Cyvian\Src\app\Classes\Locale;
use Cyvian\Src\app\Classes\Section;
use Cyvian\Src\app\Classes\Translations\EntryTypeTranslation;
use Cyvian\Src\app\Classes\Translations\SectionTranslation;
use Cyvian\Src\app\Handlers\EntryType\CreateEntryType;
use Cyvian\Src\App\Handlers\Locale\GetLocalesByType;
use Cyvian\Src\app\Repositories\ActionEntryTypeRoleRepository;
use Cyvian\Src\app\Repositories\ActionRepository;
use Cyvian\Src\app\Repositories\ActionTranslationRepository;
use Cyvian\Src\app\Repositories\EntryTypeRepository;
use Cyvian\Src\app\Repositories\EntryTypeTranslationRepository;
use Cyvian\Src\app\Repositories\FieldAttributeRepository;
use Cyvian\Src\app\Repositories\FieldRepository;
use Cyvian\Src\app\Repositories\FieldValueRepository;
use Cyvian\Src\app\Repositories\LocaleRepository;
use Cyvian\Src\app\Repositories\SectionRepository;
use Cyvian\Src\app\Repositories\SectionTranslationRepository;
use Cyvian\Src\app\Utils\Localisation;

class NewsSeeder
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

        $localesCms = $getLocalesByType->handle(Locale::IS_CMS);

        $entryType = new EntryType(
            'news',
            'model',
            1,
            new EntryTypeTranslation(Localisation::mapTranslation('cyvian.entry_types.news.singular_name', [], $localesCms), Localisation::mapTranslation('cyvian.entry_types.news.plural_name', [], $localesCms)),
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
                                'name',
                                Localisation::mapTranslation('cyvian.fields.title', [], $localesCms),
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
                            new Slug(
                                'slug',
                                Localisation::mapTranslation('cyvian.fields.slug', [], $localesCms),
                                Localisation::mapEmpty($localesCms),
                                true,
                                6,
                                true,
                                1,
                                null,
                                [],
                                new FieldPermissions(),
                                true
                            ),
                            new Boolean(
                                'boolKey',
                                Localisation::mapTranslation('cyvian.fields.boolKey', [], $localesCms),
                                Localisation::mapEmpty($localesCms),
                                true,
                                6,
                                false,
                                true,
                                [],
                                new FieldPermissions(),
                                true
                            ),
                            new Date(
                                'date',
                                Localisation::mapTranslation('cyvian.fields.date', [], $localesCms),
                                Localisation::mapEmpty($localesCms),
                                true,
                                6,
                                true,
                                false,
                                false,
                                true,
                                [],
                                new FieldPermissions(),
                                true
                            ),
                            new Datetime(
                                'datetime',
                                Localisation::mapTranslation('cyvian.fields.datetime', [], $localesCms),
                                Localisation::mapEmpty($localesCms),
                                true,
                                6,
                                true,
                                false,
                                false,
                                true,
                                [],
                                new FieldPermissions(),
                                true
                            ),
                            new Email(
                                'email',
                                Localisation::mapTranslation('cyvian.fields.email', [], $localesCms),
                                Localisation::mapEmpty($localesCms),
                                true,
                                6,
                                false,
                                1,
                                null,
                                [],
                                new FieldPermissions(),
                                true
                            ),
                            new File(
                                'file',
                                Localisation::mapTranslation('cyvian.fields.file', [], $localesCms),
                                Localisation::mapEmpty($localesCms),
                                true,
                                6,
                                true,
                                [],
                                new FieldPermissions(),
                                true
                            ),
                            new Image(
                                'image',
                                Localisation::mapTranslation('cyvian.fields.image', [], $localesCms),
                                Localisation::mapEmpty($localesCms),
                                true,
                                6,
                                true,
                                [],
                                new FieldPermissions(),
                                true
                            ),
                            new ManyEntries(
                                'manyEntries',
                                Localisation::mapTranslation('cyvian.fields.many_entries', [], $localesCms),
                                Localisation::mapEmpty($localesCms),
                                true,
                                6,
                                false,
                                false,
                                [1],
                                1,
                                null,
                                [],
                                new FieldPermissions(),
                                true
                            ),
                            new Number(
                                'number',
                                Localisation::mapTranslation('cyvian.fields.number', [], $localesCms),
                                Localisation::mapEmpty($localesCms),
                                true,
                                6,
                                true,
                                false,
                                false,
                                4,
                                null,
                                [],
                                new FieldPermissions(),
                                true
                            ),
                            new OneEntry(
                                'oneEntry',
                                Localisation::mapTranslation('cyvian.fields.one_entry', [], $localesCms),
                                Localisation::mapEmpty($localesCms),
                                true,
                                true,
                                6,
                                false,
                                true,
                                [1],
                                [],
                                new FieldPermissions(),
                                true
                            ),
                            new Password(
                                'password',
                                Localisation::mapTranslation('cyvian.fields.password', [], $localesCms),
                                Localisation::mapEmpty($localesCms),
                                true,
                                6,
                                null,
                                Localisation::mapEmpty($localesCms),
                                1,
                                null,
                                [],
                                new FieldPermissions(),
                                true
                            ),
                            new Repeater(
                                'repeater',
                                Localisation::mapTranslation('cyvian.fields.repeater', [], $localesCms),
                                Localisation::mapEmpty($localesCms),
                                true,
                                2,
                                null,
                                [
                                    new Email(
                                        'email',
                                        Localisation::mapTranslation('cyvian.fields.email', [], $localesCms),
                                        Localisation::mapEmpty($localesCms),
                                        true,
                                        6,
                                        false,
                                        1,
                                        null,
                                        [],
                                        new FieldPermissions(),
                                        true
                                    ),
                                    new Text(
                                        'text',
                                        Localisation::mapTranslation('cyvian.fields.text', [], $localesCms),
                                        Localisation::mapEmpty($localesCms),
                                        false,
                                        6,
                                        false,
                                        null,
                                        Localisation::mapEmpty(),
                                        1,
                                        null,
                                        [],
                                        new FieldPermissions(),
                                        true
                                    ),
                                    new Repeater(
                                      'repeater',
                                        Localisation::mapTranslation('cyvian.fields.repeater', [], $localesCms),
                                        Localisation::mapEmpty($localesCms),
                                        true,
                                        2,
                                        null,
                                        [
                                            new Text(
                                                'text',
                                                Localisation::mapTranslation('cyvian.fields.text', [], $localesCms),
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
                                            new Wysiwyg(
                                                'description',
                                                Localisation::mapTranslation('cyvian.fields.description', [], $localesCms),
                                                Localisation::mapEmpty($localesCms),
                                                true,
                                                6,
                                                1,
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
//                                    new Flexible(
//                                        'flexible',
//                                        Localisation::mapTranslation('cyvian.fields.flexible', [], $localesCms),
//                                        Localisation::mapEmpty($localesCms),
//                                        true,
//                                        0,
//                                        3,
//                                        [
//                                            new FlexibleSection(
//                                                'section_1',
//                                                Localisation::mapTranslation('cyvian.fields.flexible_sections.section_1', [], $localesCms),
//                                                [
//                                                    new Text(
//                                                        'text',
//                                                        Localisation::mapTranslation('cyvian.fields.text', [], $localesCms),
//                                                        Localisation::mapEmpty($localesCms),
//                                                        true,
//                                                        6,
//                                                        false,
//                                                        null,
//                                                        Localisation::mapEmpty($localesCms),
//                                                        1,
//                                                        null,
//                                                        [],
//                                                        new FieldPermissions(),
//                                                        true
//                                                    ),
//                                                    new Wysiwyg(
//                                                        'description',
//                                                        Localisation::mapTranslation('cyvian.fields.description', [], $localesCms),
//                                                        Localisation::mapEmpty($localesCms),
//                                                        true,
//                                                        6,
//                                                        1,
//                                                        null,
//                                                        [],
//                                                        new FieldPermissions(),
//                                                        true
//                                                    )
//                                                ]
//                                            )
//                                        ],
//                                        [],
//                                        new FieldPermissions(),
//                                        true
//                                    )
                                ],
                                [],
                                new FieldPermissions(),
                                true
                            ),
                            new Sections(
                                'sections',
                                Localisation::mapTranslation('cyvian.fields.sections', [], $localesCms),
                                Localisation::mapEmpty($localesCms),
                                true,
                                1,
                                null,
                                [],
                                new FieldPermissions(),
                                true
                            ),
                            new SelectMany(
                                'selectMany',
                                Localisation::mapTranslation('cyvian.fields.select_many', [], $localesCms),
                                Localisation::mapEmpty($localesCms),
                                true,
                                6,
                                false,
                                true,
                                1,
                                null,
                                [
                                    'option1' => Localisation::mapTranslation('cyvian.fields.options.draft', [], $localesCms),
                                    'option2' => Localisation::mapTranslation('cyvian.fields.options.published', [], $localesCms),
                                    'option3' => Localisation::mapTranslation('cyvian.fields.options.future', [], $localesCms)
                                ],
                                [],
                                new FieldPermissions(),
                                true
                            ),
                            new SelectOne(
                                'selectOne',
                                Localisation::mapTranslation('cyvian.fields.select_one', [], $localesCms),
                                Localisation::mapEmpty($localesCms),
                                true,
                                6,
                                false,
                                true,
                                true,
                                [
                                    'option1' => Localisation::mapTranslation('cyvian.fields.options.draft', [], $localesCms),
                                    'option2' => Localisation::mapTranslation('cyvian.fields.options.published', [], $localesCms),
                                    'option3' => Localisation::mapTranslation('cyvian.fields.options.future', [], $localesCms)
                                ],
                                [],
                                new FieldPermissions(),
                                true
                            ),
                            new Url(
                                'url',
                                Localisation::mapTranslation('cyvian.fields.url', [], $localesCms),
                                Localisation::mapEmpty($localesCms),
                                true,
                                6,
                                true,
                                false,
                                null,
                                Localisation::mapEmpty($localesCms),
                                [],
                                new FieldPermissions(),
                                true
                            ),
                            new Textarea(
                                'textarea',
                                Localisation::mapTranslation('cyvian.fields.textarea', [], $localesCms),
                                Localisation::mapEmpty($localesCms),
                                true,
                                6,
                                false,
                                1,
                                null,
                                [],
                                new FieldPermissions(),
                                true
                            )
                        ],
                    ),
                    new Section(
                        new SectionTranslation(
                            Localisation::mapTranslation('cyvian.sections.labels.hero', [], $localesCms),
                        ),
                        'hero',
                        Section::POSITION_LEFT,
                        [
                            new Text(
                                'text',
                                Localisation::mapTranslation('cyvian.fields.text', [], $localesCms),
                                Localisation::mapEmpty($localesCms),
                                true,
                                6,
                                false,
                                null,
                                Localisation::mapEmpty($localesCms),
                                null,
                                null,
                                [],
                                new FieldPermissions(),
                                true
                            ),
                            new Wysiwyg(
                                'description',
                                Localisation::mapTranslation('cyvian.fields.description', [], $localesCms),
                                Localisation::mapEmpty($localesCms),
                                true,
                                6,
                                null,
                                null,
                                [],
                                new FieldPermissions(),
                                true
                            ),
                            new Image(
                                'image',
                                Localisation::mapTranslation('cyvian.fields.image', [], $localesCms),
                                Localisation::mapEmpty($localesCms),
                                true,
                                6,
                                false,
                                [],
                                new FieldPermissions(),
                                true
                            )
                        ]
                    ),
                    new Section(
                        new SectionTranslation(
                            Localisation::mapTranslation('cyvian.sections.labels.content', [], $localesCms),
                        ),
                        'content',
                        Section::POSITION_LEFT,
                        [
                            new Flexible(
                                'sections',
                                Localisation::mapTranslation('cyvian.fields.flexible', [], $localesCms),
                                Localisation::mapEmpty($localesCms),
                                true,
                                0,
                                3,
                                [
                                    new FlexibleSection(
                                        'image',
                                        Localisation::mapTranslation('cyvian.fields.flexible_sections.image', [], $localesCms),
                                        [
                                            new Image(
                                                'image',
                                                Localisation::mapTranslation('cyvian.fields.image', [], $localesCms),
                                                Localisation::mapEmpty($localesCms),
                                                true,
                                                6,
                                                false,
                                                [],
                                                new FieldPermissions(),
                                                true
                                            ),
                                            new Text(
                                                'text',
                                                Localisation::mapTranslation('cyvian.fields.text', [], $localesCms),
                                                Localisation::mapEmpty($localesCms),
                                                true,
                                                6,
                                                false,
                                                null,
                                                Localisation::mapEmpty($localesCms),
                                                null,
                                                null,
                                                [],
                                                new FieldPermissions(),
                                                true
                                            ),
                                        ],
                                    ),
                                    new FlexibleSection(
                                        'bullet_points',
                                        Localisation::mapTranslation('cyvian.fields.flexible_sections.bullet_points', [], $localesCms),
                                        [
                                            new Text(
                                                'label',
                                                Localisation::mapTranslation('cyvian.fields.label', [], $localesCms),
                                                Localisation::mapEmpty($localesCms),
                                                true,
                                                6,
                                                false,
                                                null,
                                                Localisation::mapEmpty($localesCms),
                                                null,
                                                null,
                                                [],
                                                new FieldPermissions(),
                                                true
                                            ),
                                            new Url(
                                                'url',
                                                Localisation::mapTranslation('cyvian.fields.url', [], $localesCms),
                                                Localisation::mapEmpty($localesCms),
                                                true,
                                                6,
                                                true,
                                                false,
                                                null,
                                                Localisation::mapEmpty($localesCms),
                                                [],
                                                new FieldPermissions(),
                                                true
                                            )
                                        ],
                                    )
                                ],
                                [],
                                new FieldPermissions(),
                                true
                            )
                        ]
                    ),
                ],
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
            true,
            $entryType->id,
            [1]
        );
    }
}
