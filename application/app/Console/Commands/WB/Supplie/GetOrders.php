<?php

namespace App\Console\Commands\WB\Supplie;

use App\Models\Export;
use App\Models\WB\Supplier\Order;
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
    protected $signature = 'wb:supplie_orders {export}';

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
            $response = $wb->orders('supplie')->all($request);

            $response = (new ResponseParser)->parse($response);

            foreach ($response as $order) {

                Order::query()->create([
                    'date' => $order->date,
                    'last_change_date' => $order->lastChangeDate,
                    'supplier_article' => $order->supplierArticle,
                    'tech_size' => $order->techSize,
                    'barcode' => $order->barcode,
                    'total_price' => $order->totalPrice,
                    'discount_percent' => $order->discountPercent,
                    'warehouse_name' => $order->warehouseName,
                    'oblast' => $order->oblast,
                    'income_id' => $order->incomeID,
                    'od_id' => $order->odid,
                    'nm_id' => $order->nmId,
                    'subject' => $order->subject,
                    'category' => $order->category,
                    'brand' => $order->brand,
                    'is_cancel' => $order->isCancel,
                    'cancel_dt' => $order->cancel_dt,
                    'g_number' => $order->gNumber,
                    'sticker' => $order->sticker,
                    'sr_id' => $order->srid,
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
