<?php

namespace App\Console\Commands\WB;

use App\Models\Account;
use App\Models\Task;
use App\Services\DB\Manager;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Console\Command\Command as CommandAlias;

class Install extends Command
{
    //TODO
    //confirm delete database
    //level access

    protected $signature = 'wb:install {account}';

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
     * @throws Exception
     */
    public function handle(): int
    {
        $migrations = scandir(database_path('migrations/wb-new/'));

        $account = Account::query()->find($this->argument('account'));

        ((new Manager()))->init($account);

        if (!$account->is_remote) {

            if ($account->is_active === true) {

                DB::connection('pgsql')->statement("CREATE DATABASE $account->db_name;");
            } else
                throw new Exception('Account no active');
        }

        foreach ($migrations as $filename) {

            if (strlen($filename) > 5) {

                Artisan::call('migrate --database=second --path=database/migrations/wb-new/'.$filename);
            }
        }

        //TODO test
        $account->addTasksWB();

        return CommandAlias::SUCCESS;
    }
}
