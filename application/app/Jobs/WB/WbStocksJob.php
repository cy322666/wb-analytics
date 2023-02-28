<?php

namespace App\Jobs\WB;

use App\Models\Account;
use App\Models\WB\WbStock;
use App\Services\DB\Manager;
use App\Services\WB\Wildberries;
use Carbon\Carbon;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class WbStocksJob implements ShouldQueue
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
        return ['wb:stocks', $this->account->name];
    }

    public function __construct(protected Account $account) {}

    /**
     * Execute the job.
     *
     * @return void
     * @throws Exception
     */
    public function handle()
    {
        ((new Manager()))->init($this->account);

        $wbApi = (new Wildberries([
            'standard'  => $this->account->token_standard,
            'statistic' => $this->account->token_statistic,
        ]));

        $dateFrom = Carbon::parse('2022-01-01');//TODO static

        $stocksResponse = $wbApi->getSupplierStocks($dateFrom);

        $today = Carbon::now()->subHours(1)->format('Y-m-d');//TODO

        if ($stocksResponse->getStatusCode() !== 200) {

            //TODO перехватывать все эксепшены

            throw new Exception('Response code == '.$stocksResponse->getStatusCode().' : '.$ordersResponse->getReasonPhrase());
        } else {

            $stocks = json_decode(
                $stocksResponse->getBody()->getContents(), true
            );
        }

        $wbStocks = array_map(
            fn ($stock) => [
                'last_change_date' => $stock['lastChangeDate'],
                'supplier_article' => $stock['supplierArticle'],
                'tech_size' => $stock['techSize'],
                'barcode'   => $stock['barcode'],
                'quantity'  => $stock['quantity'],
                'is_supply' => $stock['isSupply'],
                'is_realization'    => $stock['isRealization'],
                'quantity_full'     => $stock['quantityFull'] ?? null,
                'quantity_not_in_orders' => $stock['quantityNotInOrders'] ?? null,
                'warehouse'         => $stock['warehouse'] ?? null,
                'warehouse_name'    => $stock['warehouseName'],
                'in_way_to_client'  => $stock['inWayToClient'] ?? null,
                'in_way_from_client'=> $stock['inWayFromClient'] ?? null,
                'nm_id'     => $stock['nmId'],
                'subject'   => $stock['subject'],
                'category'  => $stock['category'],
                'days_on_site' => $stock['daysOnSite'],
                'brand'     => $stock['brand'],
                'sc_code'   => $stock['SCCode'],
                'price'     => $stock['Price'],
                'discount'  => $stock['Discount'],
                'date'      => $today
            ],
            $stocks
        );

        array_map(
            fn ($chunk) =>
                WbStock::query()->insert($chunk),
                array_chunk($wbStocks, 1000)
        );
    }
}
