<?php

namespace Cyvian\Src\App\Http\Controllers;

use App\Managers\ActionResponse;
use Cvyian\Src\App\Handlers\Folder\CreateFolder;
use Cvyian\Src\App\Handlers\Folder\UpdateFolder;
use Cyvian\Src\App\Models\Cyvian\File;
use Cyvian\Src\App\Models\Cyvian\Folder;
use Cyvian\Src\App\Repositories\FolderRepository;
use Cyvian\Src\app\Repositories\LocaleRepository;
use Cyvian\Src\App\Utils\FileHelper;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;

class FileManagerController extends Controller
{
    public function index()
    {
        return Inertia::render('FileManager');
    }

    public function getFolders(Request $request): JsonResponse
    {
        return parent::success([
            'folders' => Folder::whereNull('folder_id')->get(),
        ]);
    }

    public function getItems(Request $request, ?int $folderId = null): JsonResponse
    {
        if ($folderId) {
            $folder = Folder::find($folderId);
            $folders = Folder::where('folder_id', $folderId)->get();
            $parentFolder = $folder->folder;
            $files = $folder->files;
        } else {
            $folder = null;
            $folders = Folder::whereNull('folder_id')->get();
            $parentFolder = null;
            $files = File::whereNull('folder_id')->get();
        }

        return parent::success([
            'folder' => $folder,
            'files' => $files,
            'folders' => $folders,
            'parent_folder' => $parentFolder
        ]);
    }

    public function storeFile(Request $request)
    {
        $file = FileHelper::storeFile($request);

        if ($file !== null) {
            return $this->setActionResponseInSessionAndBack(new ActionResponse(__('cyvian.files.upload_successful'), 'success'));
        } else {
            return $this->setActionResponseInSessionAndBack(new ActionResponse(__('cyvian.files.upload_failed'), 'error'));
        }
    }

    public function updateFile(Request $request, int $id)
    {
        $file = FileHelper::updateFile($request, $id);

        if ($file !== null) {
            return $this->setActionResponseInSessionAndBack(new ActionResponse(__('cyvian.files.update_successful'), ActionResponse::SUCCESS));
        } else {
            return $this->setActionResponseInSessionAndBack(new ActionResponse(__('cyvian.files.update_failed'), ActionResponse::ERROR));
        }
    }

    public function deleteFile(Request $request, int $id)
    {
        $response = FileHelper::deleteFile($id);

        if ($response) {
            return $this->setActionResponseInSessionAndBack(new ActionResponse(__('cyvian.files.delete_successful'), ActionResponse::SUCCESS));
        } else {
            return $this->setActionResponseInSessionAndBack(new ActionResponse(__('cyvian.files.delete_failed'), ActionResponse::ERROR));
        }
    }

    public function storeFolder(Request $request)
    {
        $createFolderHandler = new CreateFolder(new FolderRepository, new LocaleRepository);
        $handlerResponse = $createFolderHandler->handle($request->input('labels'), $request->input('folder_id'));

        if ($handlerResponse->isSuccessful) {
            return $this->setActionResponseInSessionAndBack(
                new ActionResponse(
                    __('cyvian.folders.update_successful'),
                    ActionResponse::SUCCESS
                )
            );
        } else if ($handlerResponse->data == $createFolderHandler::ERROR_MISSING_LABEL) {
            return $this->setActionResponseInSessionAndBack(
                new ActionResponse(
                    __('cyvian.folders.create_failed'),
                    ActionResponse::ERROR,
                    4000,
                    false,
                    null,
                    [
                        'name' => __('cyvian.folders.errors.missing_label')
                    ]
                )
            );
        } else if ($handlerResponse->data == $createFolderHandler  ::ERROR_NOT_UNIQUE_NAME) {
            return $this->setActionResponseInSessionAndBack(
                new ActionResponse(
                    __('cyvian.folders.create_failed'),
                    ActionResponse::ERROR,
                    4000,
                    false,
                    null,
                    [
                        'name' => __('cyvian.folders.errors.unique_name')
                    ]
                )
            );
        }
        throw new \Exception("No catched error");
    }

    public function updateFolder(Request $request, int $id)
    {
        $updateFolderHandler = new UpdateFolder(new FolderRepository, Folder::find($id));
        $handlerResponse = $updateFolderHandler->handle($request->input('labels'));

        if ($handlerResponse->success()) {
            return $this->setActionResponseInSessionAndBack(
                new ActionResponse(
                    __('cyvian.folders.update_successful'),
                    ActionResponse::SUCCESS
                )
            );
        } else if ($handlerResponse->errorCode == $updateFolderHandler::ERROR_MISSING_LABEL) {
            return $this->setActionResponseInSessionAndBack(
                new ActionResponse(
                    __('cyvian.folders.update_failed'),
                    ActionResponse::ERROR,
                    4000,
                    false,
                    null,
                    [
                        'name' => __('cyvian.folders.errors.missing_label')
                    ]
                )
            );
        } else if ($handlerResponse->errorCode == $updateFolderHandler::ERROR_NOT_UNIQUE_NAME) {
            return $this->setActionResponseInSessionAndBack(
                new ActionResponse(
                    __('cyvian.folders.update_failed'),
                    ActionResponse::ERROR,
                    4000,
                    false,
                    null,
                    [
                        'name' => __('cyvian.folders.errors.unique_name')
                    ]
                )
            );
        }
        throw new \Exception("No catched error");
    }

    public function deleteFolder(Request $request, int $id)
    {
        $response = FileHelper::deleteFolder($id);

        if ($response) {
            return $this->setActionResponseInSessionAndBack(
                new ActionResponse(
                    __('cyvian.folders.delete_successful'),
                    ActionResponse::SUCCESS
                )
            );
        } else {
            return $this->setActionResponseInSessionAndBack(
                new ActionResponse(
                    __('cyvian.folders.delete_failed'),
                    ActionResponse::SUCCESS
                )
            );
        }
    }
}
