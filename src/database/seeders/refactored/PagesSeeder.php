<?php

namespace Cyvian\Src\database\seeders\refactored;

use Cvyian\Src\App\Handlers\Action\CreateBaseActions;
use Cyvian\Src\app\Classes\EntryType;
use Cyvian\Src\app\Classes\Fields\Classes\FieldPermissions;
use Cyvian\Src\app\Classes\Fields\Slug;
use Cyvian\Src\app\Classes\Fields\Text;
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

class PagesSeeder
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
            'page',
            EntryType::TYPE_MODEL,
            1,
            new EntryTypeTranslation(Localisation::mapTranslation('cyvian.entry_types.page.singular_name', [], $localesCms), Localisation::mapTranslation('cyvian.entry_types.page.plural_name', [], $localesCms)),
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
                                'title',
                                Localisation::mapTranslation('cyvian.fields.title', [], $localesCms),
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
                                true,
                                6,
                                true,
                                null,
                                null,
                                [],
                                new FieldPermissions(),
                                true
                            ),
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
