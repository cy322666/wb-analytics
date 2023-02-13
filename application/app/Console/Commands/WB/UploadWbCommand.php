<?php

namespace App\Console\Commands\WB;

use App\Jobs\WbIncomesJob;
//use App\Jobs\WbOrdersJob;
use App\Jobs\WbPricesJob;
use App\Jobs\WbSalesJob;
use App\Jobs\WbSalesReportsJob;
use App\Jobs\WbStocksJob;
use App\Models\Account;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;

class UploadWbCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'upload:wb {--account-id=} {--db=mysql}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Создание jobs для выгрузки всех методов Wildberries';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        Config::set('database.default', $this->option('db'));

        $integration = RefIntegration::where('system_name', 'wb')->first();
        $accounts = $this->option('account-id') !== null
            ? [Account::find($this->option('account-id'))]
            : $integration->accounts()->where('is_active', true)->get();

        foreach ($accounts as $account) {
            $this->call(
                'wb:stocks',
                ['--account-id' => $account->id, '--db' => $this->option('db')]
            );
//            WbPricesJob::dispatch($account, $this->option('db'))->onQueue('WbPrices');
//            WbIncomesJob::dispatch($account, $this->option('db'))->onQueue('WbIncomes');
//            WbOrdersJob::dispatch($account, $this->option('db'))->onQueue('WbOrders');
//            WbSalesJob::dispatch($account, $this->option('db'))->onQueue('WbSales');
//            WbSalesReportsJob::dispatch($account, $this->option('db'))->onQueue('WbSalesReports');
        }

        return Command::SUCCESS;
    }
}
