<?php

namespace Cyvian\Src\app\Handlers\MenuSection;

use Cyvian\Src\app\Classes\MenuSection;
use Cyvian\Src\app\Handlers\MenuSection\MenuSectionTranslation\GetMenuSectionTranslationByMenuSectionId;
use Cyvian\Src\app\Repositories\LocaleRepository;
use Cyvian\Src\app\Repositories\MenuSectionRepository;
use Cyvian\Src\app\Repositories\MenuSectionTranslationRepository;

class GetMenuSections
{
    private $menuSectionRepository;
    private $menuSectionTranslationRepository;
    private $localeRepository;

    public function __construct(
        MenuSectionRepository $menuSectionRepository,
        MenuSectionTranslationRepository $menuSectionTranslationRepository,
        LocaleRepository $localeRepository
    )
    {
        $this->menuSectionRepository = $menuSectionRepository;
        $this->menuSectionTranslationRepository = $menuSectionTranslationRepository;
        $this->localeRepository = $localeRepository;
    }

    public function handle()
    {
        $getMenuSectionTranslationByMenuSectionId = new GetMenuSectionTranslationByMenuSectionId(
            $this->menuSectionTranslationRepository,
            $this->localeRepository
        );

        $eloquentMenuSections = $this->menuSectionRepository->getMenuSections();

        foreach ($eloquentMenuSections as $eloquentMenuSection) {
            $menuSectionTranslations = $getMenuSectionTranslationByMenuSectionId->handle($eloquentMenuSection->id);
            $menuSection = new MenuSection(
                $menuSectionTranslations,
                $menuItems
            );
            $menuSections[] = $menuSection;
        }
    }
}
