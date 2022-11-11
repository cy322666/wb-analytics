<?php

namespace App\Console\Commands\WB;

use App\Models\Export;
use App\Services\DB\Manager;
use App\Services\WB\RequestDto;
use App\Services\WB\ResponseParser;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Console\Command;
use App\Services\WB\Client as WB;
use Illuminate\Support\Facades\Log;
use Laravel\Octane\Exceptions\DdException;
use Symfony\Component\Console\Command\Command as CommandAlias;
use Throwable;

class GetOrders extends Command
{
    protected $signature = 'wb:orders {export}';

    protected $description = 'Command description';

    /**
     * @throws GuzzleException|DdException
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
        $request->start = '2022-06-30T17:14:52Z';
        $request->end  = '';

        try {
            $response = $wb->orders()->all($request);

            $response = (new ResponseParser)->parse($response);
dd($response);

            foreach ($response->orders as $order) {

                //created
            }

            return CommandAlias::SUCCESS;

        } catch (Throwable $exception) {

            Log::alert(__METHOD__.' : '.$exception->getMessage());

            return CommandAlias::FAILURE;
        }
    }
}
