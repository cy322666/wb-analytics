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
        $token   = $account->token;
        $options = json_decode($export->options);

        $dbManager = (new Manager());
        $dbManager->init($account);

        $wb = WB::init($token);

        $request = (new RequestDto());
        $request->start = '2022-09-30T17:14:52Z';
        $request->end  = '';
//        $request->take = '';
//        $request->skip = '';

        try {
            $response = $wb->orders()->all($request);

            $response = (new ResponseParser)->parse($response);

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
