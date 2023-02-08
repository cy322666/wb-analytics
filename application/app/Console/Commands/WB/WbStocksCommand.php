<?php

namespace App\Console\Commands\WB;

//use App\Jobs\ForChainJob;
use App\Jobs\WbStocksJob;
use App\Jobs\WbSupplierStocksJob;
use App\Models\Account;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Config;

class WbStocksCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'wb:stocks {--account-id=} {--db=mysql}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Получение остатков и обновление сущности 'остатки'";

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
            Bus::chain([
                (new WbStocksJob($account, $this->option('db'))),
                (new WbSupplierStocksJob($account, $this->option('db'))),
            ])
                ->onQueue('WbStocks')
                ->dispatch();
        }

        return Command::SUCCESS;
    }
}
