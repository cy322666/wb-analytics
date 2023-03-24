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

    public int $timeout = 600;

    public int $backoff = 10;

    private static string $defaultDateFrom;

    private static int $countDaysLoading = 7;

    private static array $dictSaleStatuses = [
        'S' => 'продажа',
        'R' => 'возврат',
        'D' => 'доплата',
        'A' => 'сторно продаж',
        'B' => 'сторно возврата',
    ];

    public function tags(): array
    {
        return ['wb:sales', $this->account->name];
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

        static::$defaultDateFrom = Carbon::now()->subYears(3)->format('Y-m-d');

        $dateFrom = WbOrder::query()->exists()
            ? Carbon::parse(WbOrder::query()->latest()->first()->date)->subDays(static::$countDaysLoading)
            : Carbon::parse(static::$defaultDateFrom);

        $salesResponse = $wbApi->getSupplierSales($dateFrom);

        $sales = json_decode(
            $salesResponse->getBody()->getContents(), true
        );

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
                WbSale::query()
                    ->upsert($wbSalesChunk, ['sale_id']
            ),
            array_chunk($wbSales, 1000)
        );
    }
}
