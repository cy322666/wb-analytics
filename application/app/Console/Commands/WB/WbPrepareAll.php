<?php

namespace App\Console\Commands\WB;

use App\Jobs\WB\WbOrdersJob;
use App\Models\Account;
use App\Services\WB\Wildberries;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Bus;
use Symfony\Component\Console\Command\Command as CommandAlias;
use Throwable;

class WbPrepareAll extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'wb:all {account}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Подготавливает процесс выгрузки всех заданий';

    /**
     * Execute the console command.
     *
     * @return int
     * @throws Exception
     */
    public function handle()
    {
        $account = Account::query()->find($this->argument('account'));

        $response = (new Wildberries([
            'standard'  => $account->token,
            'statistic' => $account->token,
        ]))->getObjectAll(null, 1);

        if ($response->getStatusCode() == 200) {

            //постановка очередей
            Bus::chain([
                new WbOrdersJob($account),
//                new OptimizePodcast,
//                function () {
//                    Podcast::update();
//                },
            ])->catch(function (Throwable $exception) {
                // Задание в цепочке не выполнено ...
                dump($exception->getMessage());

            })->onConnection('redis')
                ->onQueue('all')
                ->dispatch();

            return CommandAlias::SUCCESS;
        } else {

            //error какой код
        }
    }
}
