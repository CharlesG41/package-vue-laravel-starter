<?php

namespace Cyvian\Src\App\Commands;

use Cyvian\Src\database\seeders\refactored\DatabaseSeeder;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class InstallCyvianCms extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cyvian:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fresh install Cyvian CMS';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        Artisan::call('db:wipe');
        $migrationPath = 'cyvian/src/database/migrations';
        $output = Artisan::call('migrate', [
            '--path' => $migrationPath,
        ]);
        (new DatabaseSeeder())->run();
        return 0;
    }
}
