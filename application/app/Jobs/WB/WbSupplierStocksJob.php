<?php

namespace App\Jobs\WB;

use App\Models\Account;
use App\Models\WB\WbStock;
use App\Services\DB\Manager;
use App\Services\WB\Wildberries;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Config;

class WbSupplierStocksJob implements ShouldQueue
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

    public function tags(): array
    {
        return ['wb:supplier-stocks', $this->account->name];
    }

    public function __construct(protected Account $account) {}

    /**
     * @throws \Exception
     */
    public function handle()
    {
        ((new Manager()))->init($this->account);

        $wbApi = (new Wildberries([
            'standard'  => $this->account->token_standard,
            'statistic' => $this->account->token_statistic,
        ]));

        /**
         * @TODO:
         * Работает только у клиентов, у которых определен токен 'token_api'
         * или он !== 'NULL'.
         * Чтобы работало для всех клиентов, нужно добавить ключ 'token_api'.
         */
        $today = Carbon::now()->subHours(1)->format('Y-m-d');//TODO

        $dbWBStocks = WbStock::query()
            ->orderBy('date', 'DESC')
            ->get()
            ->groupBy('barcode')
            ->map(fn ($stock) => $stock[0])
            ->toArray();

        $skip = 0;
        $take = 1000;

        do {
            $supplierStocksResponse = json_decode(
                $wbApi->getStocks($skip, $take)->getBody()->getContents(), true
            );

            if ($supplierStocksResponse->getStatusCode() !== 200) {

                //TODO перехватывать все эксепшены

                throw new \Exception('Response code == '.$supplierStocksResponse->getStatusCode().' : '.$ordersResponse->getReasonPhrase());
            } else {

                $stocks = json_decode(
                    $supplierStocksResponse->getBody()->getContents(), true
                );
            }

//            if ($result->stocks === null) {
//                break;
//            }

            $supplierStocks = array_merge(
                $stocks,
                array_map(
                    fn ($stock) => [
                        'last_change_date' => null,
                        'supplier_article' => $stock['article'],
                        'tech_size' => $stock['size'],
                        'barcode'   => $stock['barcode'],
                        'quantity'  => $stock['stock'],
                        'is_supply' => null,
                        'is_realization'    => null,
                        'quantity_full'     => null,
                        'quantity_not_in_orders' => null,
                        'warehouse'         => $stock['warehouseId'],
                        'warehouse_name'    => $stock['warehouseName'],
                        'in_way_to_client'  => null,
                        'in_way_from_client'=> null,
                        'nm_id'     => $stock['nmId'],
                        'subject'   => $stock['subject'],
                        'category'  => $dbWBStocks[$stock->barcode]['category'] ?? null,
                        'days_on_site' => null,
                        'brand'     => $stock->brand,
                        'sc_code'   => null,
                        'price'     => $dbWBStocks[$stock->barcode]['price'] ?? null,
                        'discount'  => $dbWBStocks[$stock->barcode]['discount'] ?? null,
                        'is_supplier_stock' => true,
                        'date'      => $today
                    ],
                    $stocks
                )
            );

            $skip += count($result);

        } while ($skip < $result['total']);

//        WbStock::where([['account_id', $this->account->id], ['date', $today], ['is_supplier_stock', true]])->delete();

        array_map(
            fn ($chunk) =>
                WbStock::query()->insert($chunk),
                array_chunk($supplierStocks, 1000)
        );
    }
}
