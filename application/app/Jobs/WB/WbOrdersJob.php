<?php

namespace App\Jobs\WB;

use App\Models\Account;
use App\Models\WB\WbOrder;
use App\Services\DB\Manager;
use App\Services\WB\Wildberries;
use Carbon\Carbon;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class WbOrdersJob implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    //лимит попыток
    public int $tries = 1;

    //длительность выполнения
    public int $timeout = 30;

    //ожидание сек до повтора после фейла
    public int $backoff = 10;

    private static string $defaultDateFrom = '2022-02-13';
    private static int $countDaysLoading = 5;

    public function uniqueId(): string
    {
        return 'orders-account-'.$this->account->id;
    }

    public function __construct(protected Account $account) {}

    /**
     * @throws Exception
     */
    public function handle()
    {
        ((new Manager()))->init($this->account);

        $wbApi = (new Wildberries([
            'standard'  => $this->account->token_standard,
            'statistic' => $this->account->token_statistic,
        ]));

        $dateFrom = WbOrder::query()->exists()
            ? Carbon::parse(WbOrder::query()->latest()->first()->date)->subDays(2)
            : Carbon::parse(static::$defaultDateFrom);

        do {
            //@GuzzleException
            //@GuzzleHttp\Exception\ClientException
            $ordersResponse = $wbApi->getSupplierOrders($dateFrom);

            if ($ordersResponse->getStatusCode() !== 200) {

                //TODO перехватывать все эксепшены

                throw new Exception('Response code == '.$ordersResponse->getStatusCode().' : '.$ordersResponse->getReasonPhrase());
            } else {

                $orders = json_decode(
                    $ordersResponse->getBody()->getContents(), true
                );
            }

            //TODO кажется что можно проще
            $wbOrders = array_map(
                fn ($order) => [
                    'date'             => $order['date'],
                    'last_change_date' => $order['lastChangeDate'],
                    'supplier_article' => $order['supplierArticle'],
                    'tech_size'        => $order['techSize'],
                    'barcode'          => $order['barcode'],
                    'total_price'      => $order['totalPrice'],
                    'discount_percent' => $order['discountPercent'],
                    'warehouse_name'   => $order['warehouseName'],
                    'oblast'           => $order['oblast'],
                    'income_id'        => $order['incomeID'],
                    'odid'             => $order['odid'],
                    'nm_id'     => $order['nmId'],
                    'subject'   => $order['subject'],
                    'category'  => $order['category'],
                    'brand'     => $order['brand'],
                    'is_cancel' => $order['isCancel'],
                    'cancel_dt' => $order['cancel_dt'],
                    'g_number'  => $order['gNumber'],
                    'sticker'   => $order['sticker'],
                    'srid'      => $order['srid'],
                ],
                $orders
            );

            //TODO посмотреть что под капотом происходит
            array_map(
                fn ($wbOrdersChunk) =>
                    WbOrder::query()->upsert($wbOrdersChunk, [
                        'date', 'last_change_date', 'barcode', 'odid', 'g_number', 'is_cancel'
                    ]),
                    array_chunk($wbOrders, 1000)
            );
        } while (count($orders) >= 100_000);
    }

    //TODO command
    //php artisan queue:clear redis --queue=emails
    //php artisan queue:flush
}
