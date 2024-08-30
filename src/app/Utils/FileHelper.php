<?php

namespace Cyvian\Src\App\Utils;

use Cyvian\Src\App\Models\Cyvian\File as FileModel;
use Cyvian\Src\App\Models\Cyvian\Folder;
use Cyvian\Src\App\Models\Cyvian\Translations\FileTranslation;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use stdClass;
use Symfony\Component\HttpFoundation\File\File;

class FileHelper
{
    const DISK = 'public';

    static public function storeFile(Request $request): FileModel
    {
        $validator = self::validateFile($request);
        if($validator->fails()) {
            return null;
        }
        list($uploadedFile, $extension) = self::createUploadedFileFromBase64File($request->input('file'));
        $fileObject = self::storeFileModelInDatabase($request, $extension);
        self::storeFileOnDisk($uploadedFile, $fileObject);

        return $fileObject;
    }

    static private function createUploadedFileFromBase64File(string $base64File): array
    {
        $extension = explode('/', mime_content_type($base64File))[1];
        // decode the base64 file
        $fileData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $base64File));
        // save it to temporary dir first.
        $tmpFilePath = sys_get_temp_dir() . '/' . Str::uuid()->toString();
        file_put_contents($tmpFilePath, $fileData);
        // this just to help us get file info.
        $tmpFile = new File($tmpFilePath);
        $file = new UploadedFile(
            $tmpFile->getPathname(),
            $tmpFile->getFilename(),
            $tmpFile->getMimeType(),
            0,
            true // Mark it as test, since the file isn't from real HTTP POST.
        );

        return [$file, $extension];
    }

    static private function storeFileModelInDatabase(Request $request, string $extension) : FileModel
    {
        $sanitizedRequest = self::sanitizeRequest($request);
        $nameToSanitize = null;
        if($sanitizedRequest->name) {
            $nameToSanitize = $sanitizedRequest->name;
        } else {
            $explodedName = explode('.', $sanitizedRequest->filename);
            $explodedName = array_slice($explodedName, 0, -1);
            $nameToSanitize = implode('.', $explodedName);
        }
        $name = self::sanitizeName($nameToSanitize);
        $folderId = null;
        $folder = null;

        if($sanitizedRequest->folderId) {
            $folder = Folder::find($sanitizedRequest->folderId);
            $folderId = $folder->id;
        }

        $fileObject = FileModel::create([
            'name' => $name,
            'extension' => $extension,
            'disk' => self::DISK,
            'is_image' => self::isImage($extension),
            'folder_id' => $folderId
        ]);

        foreach (config('locales.locales_cms') as $locale) {
            FileTranslation::create([
                'description' => $sanitizedRequest->description->{$locale->code},
                'parent_id' => $fileObject->id,
                'locale_id' => $locale->id
            ]);
        }

        return $fileObject;
    }

    static public function updateFile(Request $request, int $id): ?FileModel
    {
        $validator = self::validateFile($request);
        if($validator->fails()) {
            return null;
        }

        $sanitizedRequest = self::sanitizeRequest($request);
        $originalFileObject = FileModel::find($id);

        if($sanitizedRequest->file) {
            list($uploadedFile, $extension) = self::createUploadedFileFromBase64File($request->input('file'));
            $originalFileObject->deleteFile();
            $updatedFileObject = self::updateFileModelInDatabase($sanitizedRequest, $originalFileObject, $extension);
            self::storeFileOnDisk($uploadedFile, $updatedFileObject);
        } else {
            $updatedFileObject = self::updateFileModelInDatabase($sanitizedRequest, $originalFileObject, $originalFileObject->extension);
            // update file name on disk
            if($updatedFileObject->name != $originalFileObject->name) {
                Storage::disk(self::DISK)->move($originalFileObject->fullPath, $updatedFileObject->fullPath);
            }
        }

        return $updatedFileObject;
    }

    static private function updateFileModelInDatabase(stdClass $sanitizedRequest, FileModel $fileObject, $extension): FileModel
    {
        $nameToSanitize = null;
        if($sanitizedRequest->name) {
            $nameToSanitize = $sanitizedRequest->name;
        } else {
            $explodedName = explode('.', $sanitizedRequest->filename);
            $explodedName = array_slice($explodedName, 0, -1);
            $nameToSanitize = implode('.', $explodedName);
        }
        $name = self::sanitizeName($nameToSanitize, $fileObject);
        $folderId = null;
        $folder = null;

        if($sanitizedRequest->folderId) {
            $folder = Folder::find($sanitizedRequest->folderId);
            $folderId = $folder->id;
        }

        $fileObject->update([
            'name' => $name,
            'extension' => $extension,
            'disk' => self::DISK,
            'is_image' => self::isImage($extension),
            'folder_id' => $folderId
        ]);
        foreach (config('locales.locales_cms') as $locale) {
            FileTranslation::create([
                'description' => $sanitizedRequest->description->{$locale->code},
                'parent_id' => $fileObject->id,
                'locale_id' => $locale->id
            ]);
        }

        return $fileObject;
    }

    static public function deleteFile(int $id): bool
    {
        $file = FileModel::find($id);
        $file->delete();

        return true;
    }

    static public function deleteFolder(int $id): bool
    {
        $folder = Folder::find($id);
        $folder->delete();

        return true;
    }

    static private function storeFileOnDisk(UploadedFile $uploadedFile, FileModel $fileModel)
    {
        $uploadedFile->storeAs($fileModel->path, $fileModel->fullName, self::DISK);
    }

    static private function sanitizeRequest(Request $request): StdClass
    {
        $values = $request->all();
        $std = new stdClass;
        foreach($values as $key => $value) {
            if($key == 'description') {
                if(!array_key_exists('description', $values) || is_string($value)) {
                    $std->description = new stdClass;
                }
                $locales = config('locales.locales');
                $descriptionValue = json_decode($value, true);
                $locales->each(function($locale) use(&$std, $descriptionValue) {
                    if(!array_key_exists($locale->code, $descriptionValue)) {
                        $std->description->{$locale->code} = null;
                    } else {
                        $std->description->{$locale->code} = $descriptionValue[$locale->code];
                    }
                });
            } elseif ($value == 'null') {
                $std->{$key} = null;
            } else {
                $std->{$key} = $value;
            }
        }

        return $std;
    }

    static private function validateFile(Request $request)
    {
        $validation = ['name' => ['string', 'nullable', 'max:50']];
        $locales = config('locales.locales');
        $descriptionValidation = $locales->mapWithKeys(function($locale) {
            $validationKey = 'description.' . $locale->code;
            return [$validationKey => ['string', 'nullable', 'max:255']];
        });
        $validation[] = $descriptionValidation;
        $inputs = $request->all();
        $inputs['description'] = json_decode($inputs['description']);

        return Validator::make($inputs, $validation);
    }

    static private function incrementName(string $name, int $i): string
    {
        if ($i === 1) {
            $name = $name .  '-' . $i;
        } else {
            $names = explode('-', strrev($name));
            $names[0] = $i;
            $name = strrev(implode('-', $names));
        }
        return $name;
    }

    static private function sanitizeName(string $name, FileModel $fileObject = null): string
    {
        $i = 1;
        $fileObjectSearched = FileModel::where('name', $name)->first();
        // si ya un autre fichier du meme nom, ou si ya un fichier du meme nom et que le id est different de $fileObject passé en parametre
        if (
            $fileObject === null && $fileObjectSearched !== null
            || $fileObject !== null && $fileObjectSearched !== null && $fileObject->id !== $fileObjectSearched->id
        ) {
            while (FileModel::where('name', $name)->first() !== null) {
                $name = self::incrementName($name, $i);
                $i++;
            }
        }

        return $name;
    }

    static private function isImage(string $extension) : bool
    {
        return in_array($extension, ['png', 'jpg', 'jpeg']);
    }

    static private function getExtension(string $name): string
    {
        $explodedName = explode('.', $name);

        return  end($explodedName);
    }
}
