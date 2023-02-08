<?php

namespace App\Jobs;

use App\Models\Account;
use App\Models\WbOrder;
use App\Models\WbSale;
use Carbon\Carbon;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Config;
use KFilippovk\Wildberries\Exceptions\WildberriesException;
use KFilippovk\Wildberries\Facades\Wildberries;
use Throwable;

class WbSalesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected Account $account;
    protected string $db;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Account $account, string $db)
    {
        $this->account = $account;
        $this->db = $db;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Config::set('database.default', $this->db);

        $maxRetryRequests = 30;

        $dictSaleStatuses = [
            'S' => 'продажа',
            'R' => 'возврат',
            'D' => 'доплата',
            'A' => 'сторно продаж',
            'B' => 'сторно возврата',
        ];

        // DEBUG
        dump('account id: ' . $this->account->id);

        $countSubtractionMonth = 3;
        $dateFrom = WbOrder::where('account_id', $this->account->id)->count()
            ? Carbon::today()->subMonths($countSubtractionMonth)
            : Carbon::parse('2022-01-01');

        $keys = $this->account->getIntegrationKeysMap();

        do {
            $countSleep = 2;
            $currentRetryRequests = 0;

            $sales = null;
            while ($currentRetryRequests < $maxRetryRequests) {
                try {
                    $currentRetryRequests++;
                    $sales = Wildberries::config($keys)->getSupplierSales($dateFrom);
                    break;
                } catch (Throwable $throwable) {
                    if ($throwable instanceof WildberriesException) {
                        dump('WB Exception: Message: '
                            . substr($throwable->getMessage(), 0, 255) . "...\n"
                            . "Sleeping on {$countSleep} seconds...");
                        sleep($countSleep);
                    } else {
                        dd($throwable->getMessage());
                    }
                }
            }

            if ($currentRetryRequests === $maxRetryRequests) {
                throw new Exception("Error: The limit of retry {$maxRetryRequests} has been reached. Stopping send request.");
            }

            if (!(is_countable($sales) && count($sales))) {
                return;
            }

            $wbSales = array_map(
                fn ($sale) =>
                [
                    'account_id' => $this->account->id,
                    'g_number' => $sale->gNumber,
                    'date' => $sale->date,
                    'last_change_date' => $sale->lastChangeDate,
                    'supplier_article' => $sale->supplierArticle,
                    'tech_size' => $sale->techSize,
                    'barcode' => $sale->barcode,
                    'total_price' => $sale->totalPrice,
                    'discount_percent' => $sale->discountPercent,
                    'is_supply' => $sale->isSupply,
                    'is_realization' => $sale->isRealization,
                    'promo_code_discount' => $sale->promoCodeDiscount,
                    'warehouse_name' => $sale->warehouseName,
                    'country_name' => $sale->countryName,
                    'oblast_okrug_name' => $sale->oblastOkrugName,
                    'region_name' => $sale->regionName,
                    'income_id' => $sale->incomeID,
                    'sale_id' => $sale->saleID,
                    'sale_id_status' => $dictSaleStatuses[substr($sale->saleID, 0, 1)],
                    'odid' => $sale->odid,
                    'spp' => $sale->spp,
                    'for_pay' => $sale->forPay,
                    'finished_price' => $sale->finishedPrice,
                    'price_with_disc' => $sale->priceWithDisc,
                    'nm_id' => $sale->nmId,
                    'subject' => $sale->subject,
                    'category' => $sale->category,
                    'brand' => $sale->brand,
                    'is_storno' => $sale->IsStorno ?? $sale->isStorno,
                    'sticker' => $sale->sticker,
                    'srid' => $sale->srid,
                ],
                $sales
            );

            $wbSalesChuncks = array_chunk($wbSales, 1000);
            array_map(
                fn ($wbSalesChunck) =>
                WbSale::upsert($wbSalesChunck, ['account_id', 'date', 'last_change_date', 'barcode', 'sale_id', 'odid', 'g_number']),
                $wbSalesChuncks
            );

            $startDate = Carbon::parse(end($sales)->lastChangeDate);

            // DEBUG
            dump((string) $startDate);
        } while (count($sales) >= 80_000);
    }
}
