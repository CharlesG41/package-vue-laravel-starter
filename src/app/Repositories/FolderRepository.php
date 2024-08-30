<?php

namespace Cyvian\Src\App\Repositories;

use Cyvian\Src\App\Models\Cyvian\Folder;
use Cyvian\Src\App\Models\Cyvian\Translations\FolderTranslation;

class FolderRepository 
{
    public function storeFolder(array $labels, ?int $parentFolderId): Folder 
    {
        $folder = Folder::create([
            'folder_id' => $parentFolderId,
        ]);

        foreach (config('locales.locales_cms') as $locale) {
            $folderLabel = $labels[$locale->code];
            FolderTranslation::create([
                'label' => $folderLabel,
                'parent_id' => $folder->id,
                'locale_id' => $locale->id
            ]);
        }

        return $folder;
    }

    public function updateFolder(Folder $folder, array $labels): Folder 
    {
        foreach($labels as $label) {
            FolderTranslation::where('parent_id', $folder->id)->update([
                'label' => $label
            ]);
        }

        return $folder;
    }

    public function deleteFolder(int $folderId)
    {
        $folder = Folder::find($folderId);
        $folder->delete();
    }
}