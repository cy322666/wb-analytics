<?php

namespace App\Jobs;

use App\Models\Account;
use App\Models\WbSalesReport;
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

class WbSalesReportsJob implements ShouldQueue
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

        $countSubtractionMonth = 3;
        $dateFrom = WbSalesReport::where('account_id', $this->account->id)->count()
            ? Carbon::today()->subMonths($countSubtractionMonth)
            : Carbon::parse('2022-01-01');
        $dateTo = Carbon::today();

        // DEBUG
        dump('account id: ' . $this->account->id);

        $rrd_id = 0;
        $limit = 50000;

        $keys = $this->account->getIntegrationKeysMap();

        do {
            // DEBUG
            dump('rrd_id: ' . $rrd_id);

            $countSleep = 2;
            $currentRetryRequests = 0;

            $salesReports = null;
            while ($currentRetryRequests < $maxRetryRequests) {
                try {
                    $currentRetryRequests++;
                    $salesReports = Wildberries::config($keys)->getSupplierReportDetailByPeriod(
                        $dateFrom,
                        $dateTo,
                        rrdid: $rrd_id,
                        limit: $limit
                    );
                    break;
                } catch (Throwable $throwable) {
                    if ($throwable instanceof WildberriesException) {
                        dump('WB Exception: Message: '
                            . substr($throwable->getMessage(), 0, 255) . "...\n"
                            . "Sleeping on {$countSleep} seconds...");
                        sleep($countSleep);
                    }
                }
            }

            if ($currentRetryRequests === $maxRetryRequests) {
                throw new Exception("Error: The limit of retry {$maxRetryRequests} has been reached. Stopping send request.");
            }

            if ($salesReports === null || !count($salesReports)) {
                return;
            }

            $wbSalesReports = array_map(
                fn ($salesReport) =>
                [
                    'account_id' => $this->account->id,
                    'realizationreport_id' => $salesReport->realizationreport_id,
                    'date_from' => $salesReport->date_from,
                    'date_to' => $salesReport->date_to,
                    'create_dt' => $salesReport->create_dt,
                    'suppliercontract_code' => $salesReport->suppliercontract_code,
                    'rrd_id' => $salesReport->rrd_id,
                    'gi_id' => $salesReport->gi_id,
                    'subject_name' => $salesReport->subject_name,
                    'nm_id' => $salesReport->nm_id,
                    'brand_name' => $salesReport->brand_name,
                    'sa_name' => $salesReport->sa_name,
                    'ts_name' => $salesReport->ts_name,
                    'barcode' => $salesReport->barcode,
                    'doc_type_name' => $salesReport->doc_type_name,
                    'quantity' => $salesReport->quantity,
                    'retail_price' => $salesReport->retail_price,
                    'retail_amount' => $salesReport->retail_amount,
                    'sale_percent' => $salesReport->sale_percent,
                    'commission_percent' => $salesReport->commission_percent,

                    'office_name' => $salesReport->office_name,
                    'supplier_oper_name' => $salesReport->supplier_oper_name,
                    'order_dt' => $salesReport->order_dt,
                    'sale_dt' => $salesReport->sale_dt,
                    'rr_dt' => $salesReport->rr_dt,
                    'shk_id' => $salesReport->shk_id,
                    'retail_price_withdisc_rub' => $salesReport->retail_price_withdisc_rub,
                    'delivery_amount' => $salesReport->delivery_amount,
                    'return_amount' => $salesReport->return_amount,
                    'delivery_rub' => $salesReport->delivery_rub,
                    'gi_box_type_name' => $salesReport->gi_box_type_name,
                    'product_discount_for_report' => $salesReport->product_discount_for_report,
                    'supplier_promo' => $salesReport->supplier_promo,
                    'rid' => $salesReport->rid,

                    'ppvz_spp_prc' => $salesReport->ppvz_spp_prc,
                    'ppvz_kvw_prc_base' => $salesReport->ppvz_kvw_prc_base,
                    'ppvz_kvw_prc' => $salesReport->ppvz_kvw_prc,
                    'ppvz_sales_commission' => $salesReport->ppvz_sales_commission,
                    'ppvz_for_pay' => $salesReport->ppvz_for_pay,
                    'ppvz_reward' => $salesReport->ppvz_reward,
                    'acquiring_fee' => $salesReport->acquiring_fee ?? null,
                    'acquiring_bank' => $salesReport->acquiring_bank ?? null,
                    'ppvz_vw' => $salesReport->ppvz_vw,
                    'ppvz_vw_nds' => $salesReport->ppvz_vw_nds,
                    'ppvz_office_id' => $salesReport->ppvz_office_id,
                    'ppvz_office_name' => $salesReport->ppvz_office_name ?? null,
                    'ppvz_supplier_id' => $salesReport->ppvz_supplier_id,
                    'ppvz_supplier_name' => $salesReport->ppvz_supplier_name,
                    'ppvz_inn' => $salesReport->ppvz_inn,

                    'declaration_number' => $salesReport->declaration_number,
                    'bonus_type_name' => $salesReport->bonus_type_name ?? null,
                    'sticker_id' => $salesReport->sticker_id,
                    'site_country' => $salesReport->site_country,
                    'penalty' => $salesReport->penalty,
                    'additional_payment' => $salesReport->additional_payment,
                    'srid' => $salesReport->srid,
                ],
                $salesReports
            );

            $wbSalesReportsChuncks = array_chunk($wbSalesReports, 1000);
            array_map(
                fn ($wbSalesReportsChunck) =>
                WbSalesReport::upsert($wbSalesReportsChunck, ['account_id', 'rrdid']),
                $wbSalesReportsChuncks
            );

            $rrd_id = end($salesReports)->rrd_id;

            // DEBUG
            dump('rr_dt: ' . end($salesReports)->rr_dt);
        } while (count($salesReports) >= $limit);
    }
}
