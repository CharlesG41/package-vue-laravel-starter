<?php

namespace Cyvian\Src\database\seeders\refactored;

use Cvyian\Src\App\Handlers\Action\CreateBaseActions;
use Cyvian\Src\app\Classes\EntryType;
use Cyvian\Src\app\Classes\Fields\Classes\FieldPermissions;
use Cyvian\Src\app\Classes\Fields\Text;
use Cyvian\Src\app\Classes\Form;
use Cyvian\Src\app\Classes\Locale;
use Cyvian\Src\app\Classes\Section;
use Cyvian\Src\app\Classes\Translations\EntryTypeTranslation;
use Cyvian\Src\app\Classes\Translations\SectionTranslation;
use Cyvian\Src\app\Handlers\EntryType\CreateEntryType;
use Cyvian\Src\App\Handlers\Locale\GetLocalesByType;
use Cyvian\Src\App\Models\General;
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

class GeneralSeeder
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
            'general',
            EntryType::TYPE_SETTING,
            4,
            new EntryTypeTranslation(Localisation::mapTranslation('cyvian.entry_types.general.singular_name', [], $localesCms), Localisation::mapTranslation('cyvian.entry_types.general.plural_name', [], $localesCms)),
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

                            )
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
            false,
            true,
            false,
            false,
            false,
            $entryType->id,
            [1]
        );
        General::create([
            'name' => [
                'en' => 'CYVIAN EN',
                'fr' => 'CYVIAN FR',
            ],
        ]);
    }
}
