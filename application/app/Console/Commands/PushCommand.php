<?php

namespace App\Console\Commands;

use App\Models\Task;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Symfony\Component\Console\Command\Command as CommandAlias;

class PushCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'push:one';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Отправляет задание в работу';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        $task = Task::query()
            ->where('completed', false)
            ->where('status', 0)
            ->where('is_active', true)
            ->first();

        if ($task) {

            Artisan::call($task->command.' '.$task->account_id);
        }

        return CommandAlias::SUCCESS;
    }
}
