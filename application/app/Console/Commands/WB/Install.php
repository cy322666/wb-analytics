<?php

namespace App\Console\Commands\WB;

use App\Models\Account;
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
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'wb:install {id}';

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

        $account = Account::query()->find($this->argument('id'));

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

        return CommandAlias::SUCCESS;
    }
}
