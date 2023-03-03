<?php

namespace App\Console\Commands\WB;

use App\Models\Account;
use App\Services\DB\Manager;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Symfony\Component\Console\Command\Command as CommandAlias;

class Migrate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'wb:migrate {account}';

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
        $account = Account::query()->find($this->argument('account'));

        $migrations = scandir(database_path('migrations/wb-new/'));

        ((new Manager()))->init($account);

        foreach ($migrations as $filename) {

            if (strlen($filename) > 5) {

                Artisan::call('migrate --database=second --path=database/migrations/wb-new/'.$filename);
            }
        }
        return CommandAlias::SUCCESS;
    }
}
