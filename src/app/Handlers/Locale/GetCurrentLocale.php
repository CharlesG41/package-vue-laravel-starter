<?php

namespace Cyvian\Src\App\Handlers\Locale;

use Cyvian\Src\app\Classes\Locale;
use Cyvian\Src\app\Handlers\HandlerResponse;
use Cyvian\Src\App\Repositories\LocaleRepository;
use Illuminate\Support\Facades\App;

class GetCurrentLocale
{
    private $localeRepository;

    public function __construct(LocaleRepository $localeRepository)
    {
        $this->localeRepository = $localeRepository;
    }

    public function handle(): Locale
    {
        $getCurrentLocaleCode = new GetCurrentLocaleCode;
        $localeCode = $getCurrentLocaleCode->handle();
        $eloquentLocale = $this->localeRepository->getLocaleByCode($localeCode);

        return new Locale(
            $eloquentLocale->id,
            $eloquentLocale->code,
            $eloquentLocale->name,
            $eloquentLocale->is_cms,
            $eloquentLocale->is_site,
            $eloquentLocale->is_default
        );
    }
}
