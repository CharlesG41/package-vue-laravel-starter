<?php

namespace Charlesg\Cms\Facades;

use Illuminate\Support\Facades\Facade;

class CmsTranslations extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'cms-translations';
    }
}