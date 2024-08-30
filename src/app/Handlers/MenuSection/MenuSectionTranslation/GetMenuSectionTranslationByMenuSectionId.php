<?php

namespace Cyvian\Src\app\Handlers\MenuSection\MenuSectionTranslation;

use Cyvian\Src\app\Classes\Locale;
use Cyvian\Src\app\Classes\Translations\MenuSectionTranslation;
use Cyvian\Src\App\Handlers\Locale\GetLocalesByType;
use Cyvian\Src\app\Repositories\LocaleRepository;
use Cyvian\Src\app\Repositories\MenuSectionTranslationRepository;
use Cyvian\Src\app\Utils\Localisation;

class GetMenuSectionTranslationByMenuSectionId
{
    private $menuSectionTranslationRepository;
    private $localeRepository;

    public function __construct(
        MenuSectionTranslationRepository $menuSectionTranslationRepository,
        LocaleRepository $localeRepository
    )
    {
        $this->menuSectionTranslationRepository = $menuSectionTranslationRepository;
        $this->localeRepository = $localeRepository;
    }

    public function handle(int $menuSectionId)
    {
        $eloquentMenuSectionTranslations = $this->menuSectionTranslationRepository->getMenuSectionTranslationByMenuSectionId($menuSectionId);

        $names = [];

        $getLocalesByType = new GetLocalesByType($this->localeRepository);
        $locales = $getLocalesByType->handle(Locale::IS_CMS);

        foreach ($locales as $locale) {
            $names[$locale->code] = $eloquentMenuSectionTranslations->where('locale_id', $locale->id)->first()->name ?? '';
        }

        return new MenuSectionTranslation(
            new Localisation($names)
        );
    }
}
