<?php

namespace App\Console\Commands\Wb;

use App\Models\Account;
use App\Models\WB\WbIncome;
use App\Models\WB\WbOrder;
use App\Models\WB\WbPrice;
use App\Models\WB\WbSale;
use App\Models\WB\WbSalesReport;
use App\Models\WB\WbStock;
use App\Services\DB\Manager;
use Filament\Notifications\Notification;
use Illuminate\Console\Command;
use Symfony\Component\Console\Command\Command as CommandAlias;

class TruncateAll extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'wb:truncate-all {account}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Чистит все wb таблицы клиента';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        $account = Account::query()->find($this->argument('account'));

        $account->tasks()->delete();

        ((new Manager()))->init($account);

        WbStock::query()->truncate();
        WbIncome::query()->truncate();
        WbOrder::query()->truncate();
        WbPrice::query()->truncate();
        WbSalesReport::query()->truncate();
        WbStock::query()->truncate();
        WbSale::query()->truncate();

        Notification::make()
            ->title('Успешно')
            ->success()
            ->send();

        return CommandAlias::SUCCESS;
    }
}
