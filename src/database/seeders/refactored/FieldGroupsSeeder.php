<?php

namespace Cyvian\Src\database\seeders\refactored;

use Cvyian\Src\App\Handlers\Action\CreateBaseActions;
use Cyvian\Src\app\Classes\Action;
use Cyvian\Src\app\Classes\EntryType;
use Cyvian\Src\app\Classes\Fields\Classes\FieldPermissions;
use Cyvian\Src\app\Classes\Fields\Fields;
use Cyvian\Src\app\Classes\Fields\File;
use Cyvian\Src\app\Classes\Fields\ManyEntries;
use Cyvian\Src\app\Classes\Fields\Sections;
use Cyvian\Src\app\Classes\Fields\Text;
use Cyvian\Src\app\Classes\Form;
use Cyvian\Src\app\Classes\Locale;
use Cyvian\Src\app\Classes\Section;
use Cyvian\Src\app\Classes\Translations\EntryTypeTranslation;
use Cyvian\Src\app\Classes\Translations\SectionTranslation;
use Cyvian\Src\app\Handlers\Action\CreateAction;
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
use Illuminate\Database\Seeder;

class FieldGroupsSeeder extends Seeder
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
        $createAction = new CreateAction(
            $actionRepository,
            $actionTranslationRepository,
            $actionEntryTypeRoleRepository,
            $fieldRepository,
            $fieldAttributeRepository,
            $localeRepository
        );

        $localesCms = $getLocalesByType->handle(Locale::IS_CMS);

        $entryType = new EntryType(
            'field_group',
            EntryType::TYPE_MODEL,
            2,
            new EntryTypeTranslation(Localisation::mapTranslation('cyvian.entry_types.field_group.singular_name', [], $localesCms), Localisation::mapTranslation('cyvian.entry_types.field_group.plural_name', [], $localesCms)),
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
                                Localisation::mapTranslation('cyvian.descriptions.name', [], $localesCms),
                                true,
                                12,
                                true,
                                null,
                                Localisation::mapEmpty($localesCms),
                                null,
                                null,
                                [],
                                new FieldPermissions(),
                                true
                            ),
                            new ManyEntries(
                                'entry_types',
                                Localisation::mapTranslation('cyvian.fields.entry_types', [], $localesCms),
                                Localisation::mapTranslation('cyvian.descriptions.entry_types', [], $localesCms),
                                false,
                                12,
                                false,
                                false,
                                [\Cyvian\Src\App\Models\Cyvian\EntryType::where('name', 'entry_type')->get()->first()->id],
                                null,
                                null,
                                [],
                                new FieldPermissions(),
                                true
                            ),
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
                    )
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
            false,
            $entryType->id,
            [1]
        );

        $importAction = new Action(
            'import',
            Action::POSITION_TOP,
            Action::ACTION_TYPE_DOWNLOAD,
            '/{entry_type}/actions/{name}',
            false,
            null,
            [1],
            []
        );
        $importAction->setEntryTypeId($entryType->id);

        $exportAction = new Action(
            'import',
            Action::POSITION_TOP,
            Action::ACTION_TYPE_EXECUTE,
            '/{entry_type}/actions/{name}',
            false,
            null,
            [1],
            [
                new File(
                    'file',
                    Localisation::mapTranslation('cyvian.fields.file', [], $localesCms),
                    Localisation::mapTranslation('cyvian.descriptions.file', [], $localesCms),
                    false,
                    12,
                    true,
                    [],
                    new FieldPermissions(),
                    true
                )
            ]
        );
        $exportAction->setEntryTypeId($entryType->id);

        $createAction->handle($importAction);
        $createAction->handle($exportAction);
    }
}
