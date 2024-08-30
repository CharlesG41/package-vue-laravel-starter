<?php

namespace Cvyian\Src\App\Handlers\Folder;

use Cyvian\Src\app\Handlers\HandlerResponse;
use Cyvian\Src\App\Repositories\FolderRepository;

class DeleteFolder extends BaseFolder
{
    private $folderRepository;

    public function __construct(FolderRepository $folderRepository)
    {
        $this->folderRepository = $folderRepository;
    }

    public function deleteFolder(int $folderId)
    {
        $this->folderRepository->deleteFolder($folderId);

        return new HandlerResponse(null, true);
    }
}
