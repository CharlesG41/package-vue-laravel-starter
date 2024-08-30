<?php

namespace Cyvian\Src\database\seeders\refactored;

use Cyvian\Src\app\Classes\Locale;
use Cyvian\Src\App\Models\Cyvian\Locale as EloquentLocale;
use Illuminate\Database\Seeder;

class LocalesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        EloquentLocale::create([
            'name' => 'Français',
            'code' => 'fr',
            'is_cms' => true,
            'is_default' => true,
            'is_site' => true,
        ]);

        EloquentLocale::create([
            'name' => 'English',
            'code' => 'en',
            'is_cms' => true,
            'is_site' => true,
            'is_default' => false,
        ]);
    }
}
