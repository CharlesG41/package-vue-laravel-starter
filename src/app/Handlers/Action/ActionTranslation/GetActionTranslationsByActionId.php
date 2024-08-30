<?php

namespace Cyvian\Src\app\Handlers\Action\ActionTranslation;

use Cyvian\Src\app\Classes\Locale;
use Cyvian\Src\app\Classes\Translations\ActionTranslation;
use Cyvian\Src\App\Handlers\Locale\GetLocalesByType;
use Cyvian\Src\App\Handlers\Locale\GetSiteLocales;
use Cyvian\Src\app\Repositories\ActionTranslationRepository;
use Cyvian\Src\app\Repositories\LocaleRepository;
use Cyvian\Src\App\Utils\Localisation;

class GetActionTranslationsByActionId
{
    private $actionTranslationRepository;
    private $localeRepository;

    public function __construct(ActionTranslationRepository $actionTranslationRepository, LocaleRepository $localeRepository)
    {
        $this->actionTranslationRepository = $actionTranslationRepository;
        $this->localeRepository = $localeRepository;
    }

    public function handle(int $actionId): ActionTranslation
    {
        $eloquentActionTranslations = $this->actionTranslationRepository->getActionTranslationByActionId($actionId);

        $labels = [];
        $messages = [];
        $actionLabels = [];

        $getLocalesByType = new GetLocalesByType($this->localeRepository);
        $locales = $getLocalesByType->handle(Locale::IS_CMS);

        foreach($locales as $locale) {
            $labels[$locale->code] = $eloquentActionTranslations->where('locale_id', $locale->id)->first()->label ?? '';
            $messages[$locale->code] = $eloquentActionTranslations->where('locale_id', $locale->id)->first()->message ?? '';
            $actionLabels[$locale->code] = $eloquentActionTranslations->where('locale_id', $locale->id)->first()->action_label ?? '';
        }

        return new ActionTranslation(
            new Localisation($labels),
            new Localisation($messages),
            new Localisation($actionLabels)
        );
    }
}
