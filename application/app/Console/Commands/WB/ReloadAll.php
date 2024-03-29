<?php

namespace App\Console\Commands\WB;

use App\Models\Account;
use Filament\Notifications\Notification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Symfony\Component\Console\Command\Command as CommandAlias;

class ReloadAll extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'wb:reload-all {account}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        $account = Account::query()->find($this->argument('account'));

        $account->tasks()->delete();

        $account->addTasksWB();

        Notification::make()
            ->title('Успешно')
            ->success()
            ->send();

        return CommandAlias::SUCCESS;
    }
}
