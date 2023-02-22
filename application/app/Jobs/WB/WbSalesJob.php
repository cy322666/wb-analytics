<?php

namespace App\Jobs\WB;

use App\Models\Account;
use App\Models\WB\WbOrder;
use App\Models\WB\WbSale;
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
use KFilippovk\Wildberries\Exceptions\WildberriesException;
use Throwable;

class WbSalesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected string $db;

    public int $tries = 1;

    public int $timeout = 30;

    public int $backoff = 10;

    private static string $defaultDateFrom = '2022-02-13';
    private static int $countDaysLoading = 5;

    private static array $dictSaleStatuses = [
        'S' => 'продажа',
        'R' => 'возврат',
        'D' => 'доплата',
        'A' => 'сторно продаж',
        'B' => 'сторно возврата',
    ];

    public function uniqueId(): string
    {
        return 'sales-account-'.$this->account->id;
    }

    public function __construct(protected Account $account) {}

    /**
     * @throws Exception
     */
    public function handle()
    {
        ((new Manager()))->init($this->account);

        $wbApi = (new Wildberries([
            'standard' => $this->account->token_standard,
            'statistic' => $this->account->token_statistic,
        ]));

        $dateFrom = WbOrder::query()->exists()
            ? Carbon::parse(WbOrder::query()->latest()->first()->date)->subDays(2)
            : Carbon::parse(static::$defaultDateFrom);

        do {
            $salesResponse = $wbApi->getSupplierSales($dateFrom);

            if ($salesResponse->getStatusCode() !== 200) {

                //TODO перехватывать все эксепшены

                throw new Exception('Response code == '.$salesResponse->getStatusCode().' : '.$salesResponse->getReasonPhrase());
            } else {

                $sales = json_decode(
                    $salesResponse->getBody()->getContents(), true
                );
            }

            $wbSales = array_map(
                fn($sale) => [
                    'g_number'  => $sale['gNumber'],
                    'date'      => $sale['date'],
                    'last_change_date' => $sale['lastChangeDate'],
                    'supplier_article' => $sale['supplierArticle'],
                    'tech_size'     => $sale['techSize'],
                    'barcode'       => $sale['barcode'],
                    'total_price'   => $sale['totalPrice'],
                    'discount_percent'  => $sale['discountPercent'],
                    'is_supply'         => $sale['isSupply'],
                    'is_realization'    => $sale['isRealization'],
                    'promo_code_discount'   => $sale['promoCodeDiscount'],
                    'warehouse_name'        => $sale['warehouseName'],
                    'country_name'      => $sale['countryName'],
                    'oblast_okrug_name' => $sale['oblastOkrugName'],
                    'region_name'       => $sale['regionName'],
                    'income_id'         => $sale['incomeID'],
                    'sale_id'           => $sale['saleID'],
                    'sale_id_status' => static::$dictSaleStatuses[substr($sale['saleID'], 0, 1)],
                    'odid'      => $sale['odid'],
                    'spp'       => $sale['spp'],
                    'for_pay'   => $sale['forPay'],
                    'finished_price'    => $sale['finishedPrice'],
                    'price_with_disc'   => $sale['priceWithDisc'],
                    'nm_id'     => $sale['nmId'],
                    'subject'   => $sale['subject'],
                    'category'  => $sale['category'],
                    'brand'     => $sale['brand'],
                    'is_storno' => $sale['IsStorno'] ?? $sale['isStorno'],
                    'sticker'   => $sale['sticker'],
                    'srid'      => $sale['srid'],
                ],
                $sales
            );

            array_map(
                fn($wbSalesChunk) =>
                WbSale::upsert($wbSalesChunk,
                    ['date', 'last_change_date', 'barcode', 'sale_id', 'odid', 'g_number']
                ),
                array_chunk($wbSales, 1000)
            );
        } while (count($sales) >= 80_000);
    }
}
