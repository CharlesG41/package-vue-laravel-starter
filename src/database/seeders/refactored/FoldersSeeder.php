<?php

namespace Cyvian\Src\database\seeders\refactored;

use Cyvian\Src\App\Models\Cyvian\Folder;
use Cyvian\Src\App\Models\Cyvian\Locale;
use Cyvian\Src\App\Models\Cyvian\Translations\FolderTranslation;
use Illuminate\Database\Seeder;

class FoldersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $folders = [
            'images' => [
                'fr' => 'Images',
                'en' => 'Images',
            ],
            'files' => [
                'fr' => 'Fichiers',
                'en' => 'Files',
            ],
        ];
        foreach ($folders as $name => $labels) {
            $folder = Folder::create();
            foreach ($labels as $code => $label) {
                $locale = Locale::where('code', $code)->get()->first();
                FolderTranslation::create([
                    'label' => $label,
                    'parent_id' => $folder->id,
                    'locale_id' => $locale->id
                ]);
            }
        }
    }
}
