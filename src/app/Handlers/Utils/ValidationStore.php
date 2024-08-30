<?php

namespace Cyvian\Src\app\Handlers\Utils;

class ValidationStore
{
    private static $instance = null;
    public $localesAffected = [];
    public $hasError = false;

    private function __construct() {}

    public static function getInstance(): ValidationStore
    {
        if (self::$instance == null) {
            self::$instance = new ValidationStore();
        }

        return self::$instance;
    }

    public function addLocaleAffected(string $localeCode)
    {
        $this->localesAffected[] = $localeCode;
        $this->localesAffected = array_unique($this->localesAffected);
    }

    public function reset()
    {
        self::$instance = new ValidationStore();
    }
}
