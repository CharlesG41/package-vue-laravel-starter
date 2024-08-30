<?php

namespace Cyvian\Src\database\seeders\refactored;

use Cyvian\Src\App\Models\Cyvian\EntryType;
use Cyvian\Src\App\Models\Role;
use Cyvian\Src\database\seeders\refactored\DataSeeder;
use Cyvian\Src\database\seeders\refactored\EntryTypesSeeder;
use Cyvian\Src\database\seeders\refactored\FieldGroupsSeeder;
use Cyvian\Src\database\seeders\refactored\FilesSeeder;
use Cyvian\Src\database\seeders\refactored\FoldersSeeder;
use Cyvian\Src\database\seeders\refactored\GeneralSeeder;
use Cyvian\Src\database\seeders\refactored\LocalesSeeder;
use Cyvian\Src\database\seeders\refactored\MenuSectionsSeeder;
use Cyvian\Src\database\seeders\refactored\NewsSeeder;
use Cyvian\Src\database\seeders\refactored\PageSeeder;
use Cyvian\Src\database\seeders\refactored\RolesSeeder;
use Cyvian\Src\database\seeders\refactored\StringTranslationSeeder;
use Cyvian\Src\database\seeders\refactored\UsersSeeder;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        echo 'LocalesSeeder';
        (new LocalesSeeder)->run();

        echo 'MenuSectionsSeeder';
        (new MenuSectionsSeeder)->run();

        echo 'RolesSeeder';
        (new RolesSeeder)->run();

        echo 'EntryTypesSeeder';
        (new EntryTypesSeeder)->run();

        echo 'UsersSeeder';
        (new UsersSeeder)->run();

        echo 'FieldGroupsSeeder';
        (new FieldGroupsSeeder)->run();

//        echo 'FilesSeeder';
//        (new FilesSeeder)->run();

        echo 'FoldersSeeder';
        (new FoldersSeeder)->run();

        echo 'GeneralSeeder';
        (new GeneralSeeder)->run();

        echo 'StringTranslationSeeder';
        (new StringTranslationSeeder)->run();

        echo 'NewsSeeder';
        (new NewsSeeder)->run();

        echo 'PagesSeeder \\n';
        (new PagesSeeder)->run();

//        echo 'DataSeeder';
//        (new DataSeeder)->run();

        // add actions roles to EntryType
//        $adminRoleId = Role::where('slug', 'admin')->first()->id;
//        $entryType = EntryType::where('name', 'entry_type')->get()->first();
//
//        $entryTypes = EntryType::all();
//
//        foreach ($entryTypes as $et) {
//            $rows = [];
//            $et->actions()->where('position', 'general')->orWhere('position', 'top')->where('entry_type_id', $entryType->id)->get()->each(function ($action) use ($et, $adminRoleId, &$rows) {
//                $rows[] = ['action_id' => $action->id, 'entry_type_id' => $et->id, 'role_id' => $adminRoleId];
//            });
//            DB::table('action_entry_type_role')->insert($rows);
//        }
//
//        $list = [
//            'role' => [
//                'edit'
//            ],
//            'entry_type' => [
//                'edit'
//            ],
//            'user' => [
//                'edit',
//            ],
//            'field_group' => [
//                'edit'
//            ],
//            'file' => [
//                'edit'
//            ],
//            'general' => [
//                'edit',
//            ],
//            'news' => [
//                'edit'
//            ],
//            'page' => [
//                'edit'
//            ]
//        ];
//        $rows = [];
//        foreach ($entryTypes as $et) {
//            $actionsToCreate = $list[$et->name];
//            foreach ($actionsToCreate as $actionToCreate) {
//                $action = $entryType->actions->where('position', 'row')->where('name', $actionToCreate)->first();
//                $rows[] = ['action_id' => $action->id, 'entry_id' => $et->id, 'role_id' => $adminRoleId];
//            }
//        }
//        DB::table('action_entry_role')->insert($rows);

        echo '-------- END OF SEEDERS --------';
    }
}
