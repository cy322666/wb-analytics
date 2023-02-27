<?php

namespace App\Console\Commands\WB;

use App\Jobs\WB\WbOrdersJob;
use App\Jobs\WB\WbIncomesJob;
use App\Jobs\WB\WbPricesJob;
use App\Jobs\WB\WbSalesJob;
use App\Jobs\WB\WbAdvertsJob;
use App\Jobs\WB\WbAdvertsCmpJob;
use App\Jobs\WB\WbSalesReportsJob;
use App\Jobs\WB\WbStocksJob;
use App\Jobs\WB\WbSupplierStocksJob;
use App\Models\Account;
use App\Services\Telegram\Telegram;
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
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function handle()
    {
        $account = Account::query()->find($this->argument('account'));

        $response = (new Wildberries([
            'standard'  => $account->token_standard,
            'statistic' => $account->token_statistic,
        ]))->getObjectAll(null, 1);

        if ($response->getStatusCode() == 200) {

            Bus::chain([
                new WbOrdersJob($account),
                new WbIncomesJob($account),
                new WbPricesJob($account),
                new WbSalesJob($account),//TODO 429 -> token
//                new WbAdvertsJob($account),
//                new WbAdvertsCmpJob($account),
                new WbStocksJob($account),
                new WbSupplierStocksJob($account),//TODO 429
                new WbSalesReportsJob($account),
//                function () {
//                    Podcast::update();
//                },
            ])->catch(function (Throwable $exception) {

                dump($exception->getMessage().' '.$exception->getFile().' '.$exception->getLine());

//                Telegram::send($exception->getMessage());

                return CommandAlias::FAILURE;

            })->onConnection('redis')
                ->onQueue('all')
                ->dispatch();

            return CommandAlias::SUCCESS;
        } else {

            dump($response->getReasonPhrase());
            //Telegram::send($response->getStatusCode().' : '.$response->getReasonPhrase());

            return CommandAlias::FAILURE;
        }
    }
}
