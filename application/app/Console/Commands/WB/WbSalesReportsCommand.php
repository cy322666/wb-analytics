<?php

namespace App\Console\Commands\WB;

use App\Jobs\WB\WbSalesReportsJob;
use App\Models\Account;
use Illuminate\Console\Command;
use Symfony\Component\Console\Command\Command as CommandAlias;

class WbSalesReportsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'wb:sale-reports {account}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Получение отчетов о продажах и обновление сущности 'отчеты о продажах'";

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        $account = Account::query()->find($this->argument('account'));

        WbSalesReportsJob::dispatch($account)->onQueue('wb');//->afterCommit();
        //->delay();

        return CommandAlias::SUCCESS;
    }
}
