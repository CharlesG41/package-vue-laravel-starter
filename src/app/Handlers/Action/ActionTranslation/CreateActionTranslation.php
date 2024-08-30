<?php

namespace Cyvian\Src\app\Handlers\Action\ActionTranslation;

use Cyvian\Src\app\Classes\Locale;
use Cyvian\Src\app\Classes\Translations\ActionTranslation;
use Cyvian\Src\App\Handlers\Locale\GetCmsLocales;
use Cyvian\Src\App\Handlers\Locale\GetLocalesByType;
use Cyvian\Src\app\Repositories\ActionTranslationRepository;
use Cyvian\Src\App\Repositories\LocaleRepository;
use Cyvian\Src\app\Utils\Localisation;

class CreateActionTranslation
{
    private $actionTranslationRepository;
    private $localeRepository;

    public function __construct(ActionTranslationRepository $actionTranslationRepository, LocaleRepository $localeRepository)
    {
        $this->actionTranslationRepository = $actionTranslationRepository;
        $this->localeRepository = $localeRepository;
    }

    public function handle(int $actionId, ActionTranslation $actionTranslation): void
    {
        $getCmsLocales = new GetLocalesByType($this->localeRepository);
        $locales = $getCmsLocales->handle(Locale::IS_CMS);
        foreach($locales as $locale) {
            $this->actionTranslationRepository->createActionTranslation(
                $actionTranslation->labels->{$locale->code},
                $actionTranslation->messages->{$locale->code},
                $actionTranslation->actionLabels->{$locale->code},
                $actionId,
                $locale->id
            );
        }
    }
}
