<?php

namespace App\Console\Commands\WB;

use App\Models\Account;
use App\Services\DB\Manager;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Console\Command\Command as CommandAlias;

class Install extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'wb:install {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $migrations = scandir(database_path('migrations/wb/'));

        $dbName = $this->argument('name');

        $dbManager = (new Manager());
        $dbManager->init(
            Account::query()
                ->where('db_name', $dbName)
                ->first()
        );

        DB::connection('pgsql')->statement("CREATE DATABASE {$dbName};");

        foreach ($migrations as $filename) {

            if (strlen($filename) > 5) {

                Artisan::call('migrate --path=database/migrations/wb/'.$filename);
            }
        }

        return CommandAlias::SUCCESS;
    }
}
