<?php

namespace App\Console\Commands\WB;

use App\Jobs\WbIncomesJob;
use App\Models\Account;
use App\Models\RefIntegration;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;

class WbIncomesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'wb:incomes {--account-id=} {--db=mysql}';

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
        Config::set('database.default', $this->option('db'));

        $integration = RefIntegration::where('system_name', 'wb')->first();
        $accounts = $this->option('account-id') !== null
            ? [Account::find($this->option('account-id'))]
            : $integration->accounts()->where('is_active', true)->get();

        foreach ($accounts as $account) {
            WbIncomesJob::dispatch($account, $this->option('db'))->onQueue('WbIncomes');
        }

        return Command::SUCCESS;
    }
}
