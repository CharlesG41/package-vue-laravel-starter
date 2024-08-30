<?php

namespace Cyvian\Src\app\Classes;

class Locale
{
    public const IS_CMS = 'is_cms';
    public const IS_SITE = 'is_site';

    public $id;
    public $code;
    public $name;
    public $isCms;
    public $isSite;
    public $isDefault;

    public function __construct(
        int $id,
        string $code,
        string $name,
        string $isCms,
        string $isSite,
        string $isDefault
    ) {
        $this->id = $id;
        $this->code = $code;
        $this->name = $name;
        $this->isCms = $isCms;
        $this->isSite = $isSite;
        $this->isDefault = $isDefault;
    }
}
