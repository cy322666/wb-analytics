<?php

namespace App\Jobs\WB;

use App\Models\Account;
use App\Models\WbOrder;
use App\Services\DB\Manager;
use App\Services\WB\Wildberries;
use Carbon\Carbon;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Config;
//use KFilippovk\Wildberries\Exceptions\WildberriesException;
//use KFilippovk\Wildberries\Facades\Wildberries;
use Throwable;

class WbOrdersJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected string $db;

    public function __construct(protected Account $account) {}

    public function handle()
    {
        ((new Manager()))->init($this->account);

        $wbApi = (new Wildberries([
            'standard'  => $this->account->token,
            'statistic' => $this->account->token,
        ]));

        // DEBUG
        dump('account id: ' . $this->account->id);

        $countSubtractionMonth = 3;

        $dateFrom = WbOrder::query()->count()
            ? Carbon::today()->subMonths($countSubtractionMonth)
            : Carbon::parse('2022-01-01');

        do {
            $countSleep = 2;
            $currentRetryRequests = 0;
            $maxRetryRequests = 30;

            $orders = null;

            while ($currentRetryRequests < $maxRetryRequests) {
                try {
                    $currentRetryRequests++;

                    $ordersResponse = $wbApi->getSupplierOrders($dateFrom);

                    if ($ordersResponse->getStatusCode() !== 200) {

                        die('asdasd');
                    } else
                        $orders = $ordersResponse->getBody();
                    /*
                     *  $this->output = [
            'status' => $status,
            'data' => json_decode($response, true),
        ];
                     */
dd($orders);
                    break;

                } catch (Throwable $exception) {
//                    if ($throwable instanceof WildberriesException) {
                        dump('WB Exception: Message: '
                            . substr($exception->getFile().' '.$exception->getLine().' : '.$exception->getMessage(), 0, 255) . "...\n"
                            . "Sleeping on $countSleep seconds...");

                        sleep($countSleep);
//                    }
                }
            }

//            if ($currentRetryRequests === $maxRetryRequests) {
//                throw new Exception("Error: The limit of retry {$maxRetryRequests} has been reached. Stopping send request.");
//            }

//            if (!(is_countable($orders) && count($orders))) {
//                return;
//            }

            $wbOrders = array_map(
                fn ($order) =>
                [
                    'account_id' => $this->account->id,
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
                    'odid' => $order->odid,
                    'nm_id' => $order->nmId,
                    'subject' => $order->subject,
                    'category' => $order->category,
                    'brand' => $order->brand,
                    'is_cancel' => $order->isCancel,
                    'cancel_dt' => $order->cancel_dt,
                    'g_number' => $order->gNumber,
                    'sticker' => $order->sticker,
                    'srid' => $order->srid,
                ],
                (array)$orders,//TODO
            );

            $wbOrdersChuncks = array_chunk($wbOrders, 1000);
            array_map(
                fn ($wbOrdersChunck) =>
                WbOrder::upsert($wbOrdersChunck, ['account_id', 'date', 'last_change_date', 'barcode', 'odid', 'g_number', 'is_cancel']),
                $wbOrdersChuncks
            );

            $startDate = Carbon::parse(end($orders)->lastChangeDate);

            // DEBUG
            dump((string) $startDate);
        } while (count($orders) >= 80_000);
    }
}
