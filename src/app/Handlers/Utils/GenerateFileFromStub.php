<?php

namespace Cyvian\Src\app\Handlers\Utils;

use Cyvian\Src\app\Utils\Constant;
use Illuminate\Support\Facades\Log;

class GenerateFileFromStub
{
    public function handle(string $stubName, array $replaces, string $filename): void
    {
        $root = implode('/', explode('/', $_SERVER['DOCUMENT_ROOT'], -1));
        $stubFileName = $root . Constant::PATH_TO_STUBS . $stubName . '.stub';

        try {

            $stubContent = file_get_contents($stubFileName);
        } catch (\Exception $e) {
            $stubContent = '';
        }

        $replacedContent = str_replace(array_keys($replaces), array_values($replaces), $stubContent);

        try {
            file_put_contents($filename, $replacedContent);
        } catch (\Exception $e) {
            throw $e;
        }
    }
}
