<?php

namespace App\Console\Commands\WB;

use App\Jobs\WbAdvertsCmpJob;
use App\Jobs\WbAdvertsJob;
use App\Models\Account;
use App\Models\RefIntegration;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Config;

class WbAdvertsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'wb:adverts {--account-id=} {--db=mysql}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Получение данных рекламных кампаний и ставок для типа размещения на Wildberries';

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
                (new WbAdvertsJob($account, $this->option('db'))),
                (new WbAdvertsCmpJob($account, $this->option('db'))),
            ])
                ->onQueue('WbAdverts')
                ->dispatch();
        }

        return Command::SUCCESS;
    }
}
