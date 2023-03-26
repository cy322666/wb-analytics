<?php

namespace App\Console\Commands;

use App\Models\Account;
use App\Services\DB\Manager;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Symfony\Component\Console\Command\Command as CommandAlias;

class Test extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'wb:test-install {account}';

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
     * @throws \Exception
     */
    public function handle(): int
    {
        $migrations = scandir(database_path('migrations/wb-new/'));

        $account = Account::query()->find($this->argument('account'));

        ((new Manager()))->init($account);

        if (!$account->is_remote) {

            if ($account->is_active === true) {

                $result = DB::connection('pgsql')->statement("CREATE DATABASE $account->db_name;");

                dump('result query create : '.$result);

            } else
                throw new \Exception('Account no active');
        }

        dump('start migration');

        $result = Artisan::call('wb:migrate '. $account->id);

        dump('result migration : '.$result);

        return CommandAlias::SUCCESS;
    }
}
