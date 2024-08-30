<?php

namespace App\Console\Commands;

use Illuminate\Foundation\Console\MigrateCommand;
use Illuminate\Support\Facades\Artisan;

class CustomMigrateCommand extends MigrateCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate-cms {--seed : Indicates if the seed task should be re-run}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Drop all tables and re-run all migrations, and run custom seeds';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        parent::handle();

        if ($this->option('seed')) {
            Artisan::call('db:seed-custom');
        }
    }
}
