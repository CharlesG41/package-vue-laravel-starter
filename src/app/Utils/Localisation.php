<?php

namespace Cyvian\Src\app\Utils;

use Cyvian\Src\app\Classes\Locale;
use Cyvian\Src\App\Handlers\Locale\GetCurrentLocale;
use Cyvian\Src\App\Handlers\Locale\GetLocalesByType;
use Cyvian\Src\App\Repositories\LocaleRepository;

class Localisation implements \JsonSerializable
{
    public function __construct(array $data = [], array $locales = [])
    {
        if (empty($locales)) {
            $getLocalesByType = new GetLocalesByType(
                new LocaleRepository
            );
            $locales = $getLocalesByType->handle(Locale::IS_CMS);
        }

        foreach ($locales as $locale) {
            $value = $data[$locale->code] ?? '';
            $this->{$locale->code} = $value;
        }
    }

    public function getCurrent(?Locale $currentLocale = null)
    {
        if ($currentLocale === null) {
            $getCurrentLocale = new GetCurrentLocale(new LocaleRepository);
            $currentLocale = $getCurrentLocale->handle();
        }

        return $this->{$currentLocale->code};
    }

    static public function mapTranslation(string $key, array $replaces = [], array $locales = []): self
    {
        if (empty($locales)) {
            $getLocalesByType = new GetLocalesByType(
                new LocaleRepository
            );
            $locales = $getLocalesByType->handle(Locale::IS_CMS);
        }

        $translations = [];
        foreach ($locales as $locale) {
            $translations[$locale->code] = __($key, $replaces, $locale->code);
        }

        return new self($translations);
    }

    static public function mapEmpty(array $locales = []): self
    {
        return new self([], $locales);
    }

    public function jsonSerialize(): array
    {
        return get_object_vars($this);
    }

    public function __toString(): string
    {
        return json_encode($this->jsonSerialize());
    }

    public function toArray(): array
    {
        return $this->jsonSerialize();
    }
}
