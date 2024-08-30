<?php

namespace Cyvian\Src\app\Handlers;

class HandlerResponse
{
    public $isSuccessful;
    public $errorCode;
    public $data;

    public function __construct($data, bool $isSuccessful, string $errorCode = null)
    {
        $this->data = $data;
        $this->isSuccessful = $isSuccessful;
        $this->errorCode = $errorCode;
    }
}
