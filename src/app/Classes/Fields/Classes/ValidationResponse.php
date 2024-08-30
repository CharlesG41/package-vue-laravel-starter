<?php

namespace Cyvian\Src\app\Classes\Fields\Classes;

class ValidationResponse
{
    public $isValid;
    public $errorMessage;

    public function __construct(bool $isValid, $errorMessage)
    {
        $this->isValid = $isValid;
        $this->errorMessage = $errorMessage;
    }
}
