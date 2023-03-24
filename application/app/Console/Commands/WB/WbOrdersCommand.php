<?php

namespace App\Console\Commands\WB;

use App\Jobs\WB\WbOrdersJob;
use App\Models\Account;
use App\Models\Task;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Symfony\Component\Console\Command\Command as CommandAlias;

class WbOrdersCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'wb:orders {account}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Получение заказов и обновление сущности 'заказы'";
    /**
     * @var Builder|Builder[]|Collection|Model|null
     */

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        $account = Account::query()->find($this->argument('account'));

        WbOrdersJob::dispatch($account)
            ->onQueue('wb');
//            ->delay(Carbon::parse($account->time_load)->timezone('Europe/Moscow'))
//            ->afterCommit();

        return CommandAlias::SUCCESS;
    }
}
