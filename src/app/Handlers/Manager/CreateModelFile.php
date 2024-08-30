<?php

namespace Cyvian\Src\app\Handlers\Manager;

use Cyvian\Src\app\Handlers\Utils\GenerateFileFromStub;
use Cyvian\Src\App\Utils\Helper;

class CreateModelFile
{
    public function handle(string $entryTypeName)
    {
        $generateFileFromStub = new GenerateFileFromStub();
        $filename = Helper::root() . '/app/Models/' . ucfirst($entryTypeName) . '.php';
        if (!file_exists($filename)) {
            $replaces = [
                '{{ className }}' => ucfirst($entryTypeName),
                '{{ entryType }}' => $entryTypeName,
            ];
            $generateFileFromStub->handle('model', $replaces, $filename);
        }
    }
}
