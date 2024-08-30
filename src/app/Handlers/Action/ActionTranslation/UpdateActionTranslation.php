<?php

namespace Cyvian\Src\app\Handlers\Action\ActionTranslation;

use Cyvian\Src\app\Classes\Locale;
use Cyvian\Src\app\Classes\Translations\ActionTranslation;
use Cyvian\Src\App\Handlers\Locale\GetCmsLocales;
use Cyvian\Src\App\Handlers\Locale\GetLocalesByType;
use Cyvian\Src\app\Repositories\ActionTranslationRepository;
use Cyvian\Src\App\Repositories\LocaleRepository;
use Cyvian\Src\App\Utils\Localisation;

class UpdateActionTranslation
{
    private $actionTranslationRepository;

    public function __construct(ActionTranslationRepository $actionTranslationRepository)
    {
        $this->actionTranslationRepository = $actionTranslationRepository;
    }

    public function handle(ActionTranslation $actionTranslation): void
    {
        $getLocalesByType = new GetLocalesByType(new LocaleRepository);
        $locales = $getLocalesByType->handle(Locale::IS_CMS);

        foreach($locales as $locale) {
            $this->actionTranslationRepository->updateActionTranslation(
                $actionTranslation->labels->{$locale->code},
                $actionTranslation->messages->{$locale->code},
                $actionTranslation->actionLabels->{$locale->code},
                $actionTranslation->actionId,
                $locale->id
            );
        }
    }
}
