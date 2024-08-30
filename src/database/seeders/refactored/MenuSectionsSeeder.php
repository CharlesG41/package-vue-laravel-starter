<?php

namespace Cyvian\Src\database\seeders\refactored;

use Cvyian\Src\App\Handlers\Action\CreateBaseActions;
use Cyvian\Src\App\Handlers\Locale\GetLocalesByType;
use Cyvian\Src\App\Models\Cyvian\Locale;
use Cyvian\Src\App\Models\Cyvian\MenuSection;
use Cyvian\Src\App\Models\Cyvian\Translations\MenuSectionTranslation;
use Cyvian\Src\app\Repositories\LocaleRepository;
use Cyvian\Src\App\Utils\LocaleHelper;
use Cyvian\Src\app\Utils\Localisation;
use Illuminate\Database\Seeder;

class MenuSectionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $getLocalesByType = new GetLocalesByType(
            new LocaleRepository
        );

        $localesCms = $getLocalesByType->handle(\Cyvian\Src\app\Classes\Locale::IS_CMS);
        $menus = [
            Localisation::mapTranslation('cyvian.menu_sections.models', [], $localesCms),
            Localisation::mapTranslation('cyvian.menu_sections.configuration', [], $localesCms),
            Localisation::mapTranslation('cyvian.menu_sections.security', [], $localesCms),
            Localisation::mapTranslation('cyvian.menu_sections.settings', [], $localesCms),
        ];

        $locales = Locale::all();
        foreach ($menus as $key => $value) {
            $parent = MenuSection::create([
                'order' => $key
            ]);
            foreach ($locales as $locale) {
                MenuSectionTranslation::create([
                    'name' => $value->{$locale->code},
                    'locale_id' => $locale->id,
                    'parent_id' => $parent->id
                ]);
            }
        }
    }
}
