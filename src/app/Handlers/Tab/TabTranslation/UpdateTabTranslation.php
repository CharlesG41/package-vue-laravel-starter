<?php

namespace Cyvian\Src\app\Handlers\Tab\TabTranslation;

use Cyvian\Src\app\Classes\Locale;
use Cyvian\Src\app\Classes\Translations\TabTranslation;
use Cyvian\Src\App\Handlers\Locale\GetLocalesByType;
use Cyvian\Src\app\Repositories\LocaleRepository;
use Cyvian\Src\app\Repositories\TabTranslationRepository;

class UpdateTabTranslation
{
    private $tabTranslationRepository;

    public function __construct(TabTranslationRepository $tabTranslationRepository)
    {
        $this->tabTranslationRepository = $tabTranslationRepository;
    }

    public function handle(TabTranslation $tabTranslation)
    {
        $getLocalesByType = new GetLocalesByType(new LocaleRepository);
        $locales = $getLocalesByType->handle(Locale::IS_CMS);

        foreach($locales as $locale) {
            $this->tabTranslationRepository->updateTabTranslation(
                $tabTranslation->id,
                $tabTranslation->labels->{$locale->code},
                $locale->id
            );
        }
    }
}
