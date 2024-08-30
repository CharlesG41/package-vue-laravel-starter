<?php

namespace Cyvian\Src\database\seeders\refactored;

use Cvyian\Src\App\Handlers\Action\CreateBaseActions;
use Cyvian\Src\app\Classes\EntryType;
use Cyvian\Src\app\Classes\Fields\Classes\FieldPermissions;
use Cyvian\Src\app\Classes\Fields\Email;
use Cyvian\Src\app\Classes\Fields\ManyEntries;
use Cyvian\Src\app\Classes\Fields\Password;
use Cyvian\Src\app\Classes\Fields\Text;
use Cyvian\Src\app\Classes\Form;
use Cyvian\Src\app\Classes\Locale;
use Cyvian\Src\app\Classes\Section;
use Cyvian\Src\app\Classes\Tab;
use Cyvian\Src\app\Classes\Translations\EntryTypeTranslation;
use Cyvian\Src\app\Classes\Translations\SectionTranslation;
use Cyvian\Src\app\Classes\Translations\TabTranslation;
use Cyvian\Src\app\Handlers\Entry\CreateEntry;
use Cyvian\Src\app\Handlers\EntryType\CreateEntryType;
use Cyvian\Src\App\Handlers\Locale\GetLocalesByType;
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
use Cyvian\Src\app\Utils\Localisation;

class UsersSeeder
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
            'user',
            EntryType::TYPE_MODEL,
            3,
            new EntryTypeTranslation(Localisation::mapTranslation('cyvian.entry_types.user.singular_name', [], $localesCms), Localisation::mapTranslation('cyvian.entry_types.user.plural_name', [], $localesCms)),
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
                                false,
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
                            new Email(
                                'email',
                                Localisation::mapTranslation('cyvian.fields.email', [], $localesCms),
                                Localisation::mapEmpty($localesCms),
                                false,
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
                    new Section(
                        new SectionTranslation(
                            Localisation::mapEmpty($localesCms),
                        ),
                        null,
                        Section::POSITION_LEFT,
                        [
                            new Password(
                                'password',
                                Localisation::mapTranslation('cyvian.fields.password', [], $localesCms),
                                Localisation::mapEmpty($localesCms),
                                false,
                                6,
                                null,
                                Localisation::mapEmpty($localesCms),
                                null,
                                null,
                                [],
                                new FieldPermissions(),
                                true
                            ),
                            new Password(
                                'api_token',
                                Localisation::mapTranslation('cyvian.fields.api_token', [], $localesCms),
                                Localisation::mapEmpty($localesCms),
                                false,
                                12,
                                null,
                                Localisation::mapEmpty($localesCms),
                                null,
                                null,
                                [],
                                new FieldPermissions(
                                    true,
                                    true,
                                    false,
                                ),
                                true
                            ),
                            new ManyEntries(
                                'roles',
                                Localisation::mapTranslation('cyvian.fields.roles', [], $localesCms),
                                Localisation::mapEmpty($localesCms),
                                false,
                                12,
                                true,
                                true,
                                [1],
                                null,
                                null,
                                [],
                                new FieldPermissions(),
                                true
                            ),
                            new Text(
                                'preferred_locale',
                                Localisation::mapTranslation('cyvian.fields.preferred_locale', [], $localesCms),
                                Localisation::mapEmpty($localesCms),
                                false,
                                12,
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
        User::create([
            'name' => 'Charles Giguere',
            'email' => 'junk141702@gmail.com',
            'password' => 'allo',
            'api_token' => null,
            'roles' => [1],
            'preferred_locale' => 'fr'
        ]);

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
