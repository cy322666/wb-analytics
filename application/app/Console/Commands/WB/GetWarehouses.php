<?php

namespace App\Console\Commands\WB;

use Illuminate\Console\Command;
use Symfony\Component\Console\Command\Command as CommandAlias;

class GetWarehouses extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'wb:warehouses';

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
    public function handle()
    {
        return CommandAlias::SUCCESS;
    }
}
