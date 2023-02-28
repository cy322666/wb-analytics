<?php

namespace App\Console\Commands\WB;

use App\Jobs\WB\WbPricesJob;
use App\Jobs\WB\WbStocksJob;
use App\Models\Account;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use Symfony\Component\Console\Command\Command as CommandAlias;

class WbPricesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'wb:prices {account}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Получение информации о ценах';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        $account = Account::query()->find($this->argument('account'));

        WbPricesJob::dispatch($account)->onQueue('wb');//->afterCommit();
        //->delay();


        return CommandAlias::SUCCESS;
    }
}
