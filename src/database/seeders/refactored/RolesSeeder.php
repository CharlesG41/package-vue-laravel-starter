<?php

namespace Cyvian\Src\database\seeders\refactored;

use Cvyian\Src\app\Handlers\Action\CreateBaseActions;
use Cyvian\Src\app\Classes\Entry;
use Cyvian\Src\app\Classes\EntryType;
use Cyvian\Src\app\Classes\Fields\Boolean;
use Cyvian\Src\app\Classes\Fields\Classes\FieldPermissions;
use Cyvian\Src\app\Classes\Fields\Slug;
use Cyvian\Src\app\Classes\Fields\Text;
use Cyvian\Src\app\Classes\Form;
use Cyvian\Src\app\Classes\Locale;
use Cyvian\Src\app\Classes\Section;
use Cyvian\Src\app\Classes\Translations\EntryTypeTranslation;
use Cyvian\Src\app\Classes\Translations\SectionTranslation;
use Cyvian\Src\app\Handlers\Entry\CreateEntry;
use Cyvian\Src\app\Handlers\EntryType\CreateEntryType;
use Cyvian\Src\App\Handlers\Locale\GetLocalesByType;
use Cyvian\Src\app\Handlers\Utils\MergeValuesArrayIntoForm;
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
use Cyvian\Src\app\Utils\Localisation;
use Illuminate\Database\Seeder;

class RolesSeeder extends Seeder
{
    public function run()
    {
        $entryTypeRepository = new EntryTypeRepository;
        $entryTypeTranslationRepository = new EntryTypeTranslationRepository;
        $entryRepository = new EntryRepository;
        $actionRepository = new ActionRepository;
        $actionTranslationRepository = new ActionTranslationRepository;
        $actionEntryTypeRoleRepository = new ActionEntryTypeRoleRepository;
        $actionEntryRoleRepository = new ActionEntryRoleRepository;
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

        $mergeValuesArrayIntoForm = new MergeValuesArrayIntoForm;

        $createEntry = new CreateEntry(
            $entryRepository,
            $actionEntryRoleRepository,
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
            'role',
            'model',
            3,
            new EntryTypeTranslation(Localisation::mapTranslation('cyvian.entry_types.role.singular_name', [], $localesCms), Localisation::mapTranslation('cyvian.entry_types.role.plural_name', [], $localesCms)),
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
                                Localisation::mapTranslation('cyvian.fields.name', [], $localesCms),
                                Localisation::mapEmpty($localesCms),
                                true,
                                6,
                                true,
                                null,
                                Localisation::mapEmpty($localesCms),
                                null,
                                null,
                                [],
                                new FieldPermissions(),
                                true
                            ),
                            new Slug(
                                'slug',
                                Localisation::mapTranslation('cyvian.fields.slug', [], $localesCms),
                                Localisation::mapEmpty($localesCms),
                                false,
                                6,
                                false,
                                null,
                                null,
                                [],
                                new FieldPermissions(),
                                true
                            ),
                            new Boolean(
                                'super_admin',
                                Localisation::mapTranslation('cyvian.fields.super_admin', [], $localesCms),
                                Localisation::mapEmpty($localesCms),
                                false,
                                6,
                                true,
                                true,
                                [],
                                new FieldPermissions(),
                                true
                            )
                        ]
                    )

                ],
            ),
            []
        );
        $entryType = $createEntryType->handle($entryType);

        $values = [
            'name' => Localisation::mapTranslation('cyvian.roles.administrator', [], $localesCms)->toArray(),
            'slug' => 'admin',
            'super_admin' => true
        ];
        $form = $mergeValuesArrayIntoForm->handle($values, $entryType->form);
        $entry = new Entry(
            0,
            $entryType->id,
            1,
            1,
            [],
            $form
        );
        $adminRoleEntry = $createEntry->handle($entry)->data;

        $createBaseActions->handle(
            true,
            true,
            true,
            true,
            true,
            false,
            false,
            $entryType->id,
            [$adminRoleEntry->id]
        );
    }
}
