<?php

namespace App\Console\Commands\WB\Supplie;

use App\Models\Export;
use App\Models\WB\Supplier\Order;
use App\Models\WB\Supplier\Stock;
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

class GetStocks extends Command
{
    protected $signature = 'wb:supplie_stocks {export}';

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
            $response = $wb->stocks('supplie')->all($request);

            $response = (new ResponseParser)->parse($response);

            foreach ($response as $stock) {

                Stock::query()->create([
                    'last_change_date' => $stock->lastChangeDate,
                    'supplier_article' => $stock->supplierArticle,
                    'tech_size' => $stock->techSize,
                    'barcode' => $stock->barcode,
                    'quantity' => $stock->quantity,
                    'is_supply' => $stock->isSupply,
                    'is_realization' => $stock->isRealization,
                    'quantity_full' => $stock->quantityFull,
                    'quantity_not_in_orders' => $stock->quantityNotInOrders,
                    'warehouse' => $stock->warehouse,
                    'warehouse_name' => $stock->warehouseName,
                    'in_way_to_client' => $stock->inWayToClient,
                    'in_way_from_client' => $stock->inWayFromClient,
                    'nm_id' => $stock->nmId,
                    'subject' => $stock->subject,
                    'category' => $stock->category,
                    'days_on_site' => $stock->daysOnSite,
                    'brand' => $stock->brand,
                    'scc_ode' => $stock->SCCode,
                    'price' => $stock->Price,
                    'discount' => $stock->Discount,
                ]);
            }

            return CommandAlias::SUCCESS;

        } catch (Throwable $exception) {
dd($exception->getMessage());
            Log::alert(__METHOD__.' : '.$exception->getMessage());

            return CommandAlias::FAILURE;
        }
    }
}
