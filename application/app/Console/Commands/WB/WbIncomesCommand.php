<?php

namespace App\Console\Commands\WB;

use App\Jobs\WB\WbIncomesJob;
use App\Models\Account;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use Symfony\Component\Console\Command\Command as CommandAlias;

class WbIncomesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'wb:incomes {account}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Получение поставок и обновление сущности 'поставки'";

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        $account = Account::query()->find($this->argument('account'));

        WbIncomesJob::dispatch($account)
            ->onQueue('wb')
            ->delay(Carbon::parse($account->time_load)->timezone('Europe/Moscow'))
            ->afterCommit();

        return CommandAlias::SUCCESS;
    }
}
