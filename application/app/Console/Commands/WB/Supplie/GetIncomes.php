<?php

namespace App\Console\Commands\WB\Supplie;

use App\Models\Export;
use App\Models\WB\Supplier\Income;
use App\Models\WB\Supplier\Order;
use App\Services\DB\Manager;
use App\Services\WB\Models\Supplie\Incomes;
use App\Services\WB\RequestDto;
use App\Services\WB\ResponseParser;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Console\Command;
use App\Services\WB\Client as WB;
use Illuminate\Support\Facades\Log;
use Laravel\Octane\Exceptions\DdException;
use Symfony\Component\Console\Command\Command as CommandAlias;
use Throwable;

class GetIncomes extends Command
{
    protected $signature = 'wb:supplie_incomes {export}';

    protected $description = 'Command description';

    /**
     * @throws GuzzleException|DdException
     */
    public function handle(): int
    {
        $export  = Export::find($this->argument('export'));
        $account = $export->account;;
        $options = json_decode($export->options);

        $dbManager = (new Manager());
        $dbManager->init($account);

        $wb = WB::init(
            $account->token,
            $account->token32,
            $account->token64
        );

        $request = (new RequestDto());
        $request->dateFrom = '2022-09-30';
//        $request->end  = '';
//        $request->take = '';
//        $request->skip = '';

        try {
            $response = $wb->incomes('supplie')->all($request);

            $response = (new ResponseParser)->parse($response);

            foreach ($response as $income) {

                Income::query()->create([
                    'income_id' => $income->incomeId,
                    'number' => $income->number,
                    'date' => $income->date,
                    'last_change_date' => $income->lastChangeDate,
                    'supplier_article' => $income->supplierArticle,
                    'tech_size' => $income->techSize,
                    'barcode' => $income->barcode,
                    'quantity' => $income->quantity,
                    'total_price' => $income->totalPrice,
                    'date_close' => $income->dateClose,
                    'warehouse_name' => $income->warehouseName,
                    'nm_id' => $income->nmId,
                    'status' => $income->status,
                ]);
            }

            return CommandAlias::SUCCESS;

        } catch (Throwable $exception) {
dd($exception->getMessage(), $income);
            Log::alert(__METHOD__.' : '.$exception->getMessage());

            return CommandAlias::FAILURE;
        }
    }
}
