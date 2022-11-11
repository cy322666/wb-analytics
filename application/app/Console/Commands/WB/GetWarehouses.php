<?php

namespace App\Console\Commands\WB;

use App\Models\Export;
use App\Services\DB\Manager;
use App\Services\WB\Client as WB;
use App\Services\WB\RequestDto;
use App\Services\WB\ResponseParser;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Laravel\Octane\Exceptions\DdException;
use Symfony\Component\Console\Command\Command as CommandAlias;
use Throwable;

class GetWarehouses extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'wb:warehouses {export}';

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
     * @throws DdException
     */
    public function handle(): int
    {
        $export  = Export::find($this->argument('export'));
        $account = $export->account;
        $options = json_decode($export->options);

        $dbManager = (new Manager());
        $dbManager->init($account);

        $wb = WB::init(
            $account->token,
            $account->token32,
            $account->token64,
        );

        $request = (new RequestDto());

        try {
            $response = $wb->warehouses()->all($request);

            $response = (new ResponseParser)->parse($response);

            foreach ($response->warehouses as $warehouses) {

                //created
            }

            return CommandAlias::SUCCESS;

        } catch (Throwable $exception) {

            Log::alert(__METHOD__.' : '.$exception->getMessage());

            return CommandAlias::FAILURE;
        }
    }
}
