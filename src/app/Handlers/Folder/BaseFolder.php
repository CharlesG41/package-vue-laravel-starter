<?php
namespace Cvyian\Src\App\Handlers\Folder;

use Cyvian\Src\App\Models\Cyvian\Folder;
use Cyvian\Src\App\Repositories\FolderRepository;

class BaseFolder
{
    protected function isFolderNameValid(array $name, int $folderId = null, int $folderIdToIgnore = null)
    {
        if ($folderId !== null) {
            $folder = Folder::where('name', $name)->where('folder_id', $folderId)->get()->first();
        } else {
            $folder = Folder::where('name', $name)->get()->first();
        }

        if ($folder !== null && $folderIdToIgnore === null || $folder !== null && $folderIdToIgnore !== null && $folderIdToIgnore !== $folder->id) {
            return false;
        } else {
            return true;
        }
    }
}