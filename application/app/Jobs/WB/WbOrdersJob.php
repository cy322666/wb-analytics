<?php

namespace App\Jobs\WB;

use App\Models\Account;
use App\Models\WB\WbOrder;
use App\Services\DB\Manager;
use App\Services\WB\Wildberries;
use Carbon\Carbon;
use Exception;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Config;
//use KFilippovk\Wildberries\Exceptions\WildberriesException;
//use KFilippovk\Wildberries\Facades\Wildberries;
use Throwable;

class WbOrdersJob implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected string $db;

    //лимит попыток
    public int $tries = 3;

    //длительность выполнения
    public int $timeout = 120;

    //ожидание сек до повтора после фейла
    public int $backoff = 30;

    private static string $defaultDateFrom = '2022-02-13';
    private static int $countSubtractionMonth = 1;

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
//        try {
            ((new Manager()))->init($this->account);

            $wbApi = (new Wildberries([
                'standard'  => $this->account->token,
                'statistic' => $this->account->token,
            ]));

            // DEBUG
            dump('account id: ' . $this->account->id);

            $dateFrom = WbOrder::query()->exists()
//                ? Carbon::today()->subMonths(static::$countSubtractionMonth)
                ? Carbon::today()->subDays(static::$countSubtractionMonth)
                : Carbon::parse(static::$defaultDateFrom);

            do {
                //@GuzzleException
                //@GuzzleHttp\Exception\ClientException
                $ordersResponse = $wbApi->getSupplierOrders($dateFrom);

                if ($ordersResponse->getStatusCode() !== 200) {

                    //TODO перехватывать все эксепшены

                    throw new Exception('Response code == '.$ordersResponse->getStatusCode().' : '.$ordersResponse->getReasonPhrase());
                } else {

                    $orders = $ordersResponse->getBody()->getContents();

                    $orders = json_decode($orders, true);
                }

                //TODO кажется что можно проще
                $wbOrders = array_map(
                    fn ($order) =>
                    [
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
                    $orders,
                );
                $wbOrdersChuncks = array_chunk($wbOrders, 1000);

                //TODO посмотреть что под капотом происходит
                array_map(
                    fn ($wbOrdersChunck) =>
                    WbOrder::query()->upsert($wbOrdersChunck, [
                        'date', 'last_change_date', 'barcode', 'odid', 'g_number', 'is_cancel'
                    ]),
                    $wbOrdersChuncks
                );

                $startDate = Carbon::parse(end($orders)->lastChangeDate);

                // DEBUG
                dump((string) $startDate);

            } while (count($orders) >= 80_000);

//        } catch (Throwable $exception) {
//
//            dd($exception->getMessage(). ' ' .$exception->getLine());
//        }
    }

    //TODO command
    //php artisan queue:clear redis --queue=emails
    //php artisan queue:flush
}
