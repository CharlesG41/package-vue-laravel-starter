<?php

namespace Cyvian\Src\app\Handlers\Manager;

use Cyvian\Src\app\Handlers\Utils\GenerateFileFromStub;
use Cyvian\Src\App\Utils\Helper;

class CreateManagerFile
{
    public function handle(string $entryTypeName, string $type)
    {
        $generateFileFromStub = new GenerateFileFromStub();

        switch ($type) {
            case "model":
                $extendsClass = 'Model';
                break;
            case "setting":
                $extendsClass = 'Setting';
                break;
        }
        $filename = Helper::root() . '/app/Managers/' . ucfirst($entryTypeName) . 'Manager.php';
        if (!file_exists($filename)) {
            $replaces = [
                '{{ modelName }}' => ucfirst($entryTypeName),
                '{{ extendsClass }}' => $extendsClass . 'BaseManager',
            ];
            $generateFileFromStub->handle('manager', $replaces, $filename);
        }
    }
}
