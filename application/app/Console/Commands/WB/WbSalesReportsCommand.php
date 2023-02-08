<?php

namespace App\Console\Commands\WB;

use App\Jobs\WbSalesReportsJob;
use App\Models\Account;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;

class WbSalesReportsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'wb:sales-reports {--account-id=} {--db=mysql}';

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
        Config::set('database.default', $this->option('db'));

        $integration = RefIntegration::where('system_name', 'wb')->first();
        $accounts = $this->option('account-id') !== null
            ? [Account::find($this->option('account-id'))]
            : $integration->accounts()->where('is_active', true)->get();

        foreach ($accounts as $account) {
            WbSalesReportsJob::dispatch($account, $this->option('db'))->onQueue('WbSalesReports');
        }

        return Command::SUCCESS;
    }
}
