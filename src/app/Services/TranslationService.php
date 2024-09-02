<?php

namespace Charlesg\app\Services;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\App;

class TranslationService
{
    protected $translations = [];
    protected $locale;

    public function __construct()
    {
        $this->locale = App::getLocale();
    }

    public function setLocale($locale)
    {
        $this->locale = $locale;
        App::setLocale($locale);
        $this->translations = [];
        return $this->getTranslations($locale);
    }

    public function getLocale()
    {
        return $this->locale;
    }

    public function loadTranslations($locale)
    {
        $path = resource_path("lang/vendor/charlesg/{$locale}");
        $files = File::files($path);

        foreach ($files as $file) {
            $filename = $file->getFilenameWithoutExtension();
            $this->translations[$filename] = include $file->getPathname();
        }

        return $this->translations;
    }

    public function getTranslations($locale)
    {
        if (empty($this->translations)) {
            $this->loadTranslations($locale);
        }

        return $this->translations;
    }

    public function translate($key, $locale, $replace = [])
    {
        $parts = explode('.', $key);
        $file = array_shift($parts);
        
        if (!isset($this->translations[$file])) {
            $this->loadTranslations($locale);
        }

        $line = $this->translations[$file];

        foreach ($parts as $part) {
            if (!isset($line[$part])) {
                return $key;
            }
            $line = $line[$part];
        }

        return $this->makeReplacements($line, $replace);
    }

    protected function makeReplacements($line, array $replace)
    {
        if (empty($replace)) {
            return $line;
        }

        foreach ($replace as $key => $value) {
            $line = str_replace(
                [':'.$key, ':'.Str::upper($key), ':'.Str::ucfirst($key)],
                [$value, Str::upper($value), Str::ucfirst($value)],
                $line
            );
        }

        return $line;
    }
}