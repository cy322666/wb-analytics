<?php

namespace App\Jobs\WB;

use App\Models\Account;
use App\Models\WB\WbSalesReport;
use App\Services\DB\Manager;
use App\Services\WB\Wildberries;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class WbSalesReportsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 1;

    public int $timeout = 720;

    public int $backoff = 10;

    private static string $defaultDateFrom;

    private static int $countDaysLoading = 5;

    public function tags(): array
    {
        return ['wb:sale-reports', $this->account->name];
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

        static::$defaultDateFrom = Carbon::now()->subYears(3)->format('Y-m-d');

        $dateFrom = WbSalesReport::query()->exists()
            ? Carbon::parse(WbSalesReport::query()->latest()->first()->date)->subDays(static::$countDaysLoading)
            : Carbon::parse(static::$defaultDateFrom);

        $salesReportsResponse = $wbApi->getSupplierReportDetailByPeriod(
            $dateFrom,
            Carbon::today(),
            limit: 80_000,
        );

        $reports = json_decode(
            $salesReportsResponse->getBody()->getContents(), true
        );

        $wbSalesReports = array_map(
            fn ($salesReport) => [
                'realizationreport_id'  => $salesReport['realizationreport_id'],
                'date_from'             => $salesReport['date_from'],
                'date_to'               => $salesReport['date_to'],
                'create_dt'             => $salesReport['create_dt'],
                'suppliercontract_code' => $salesReport['suppliercontract_code'],
                'rrd_id'                => $salesReport['rrd_id'],
                'gi_id'                 => $salesReport['gi_id'],
                'subject_name'          => $salesReport['subject_name'],
                'nm_id'         => $salesReport['nm_id'],
                'brand_name'    => $salesReport['brand_name'],
                'sa_name'       => $salesReport['sa_name'],
                'ts_name'       => $salesReport['ts_name'],
                'barcode'       => $salesReport['barcode'],
                'doc_type_name' => $salesReport['doc_type_name'],
                'quantity'      => $salesReport['quantity'],
                'retail_price'  => $salesReport['retail_price'],
                'retail_amount' => $salesReport['retail_amount'],
                'sale_percent'  => $salesReport['sale_percent'],
                'commission_percent' => $salesReport['commission_percent'],

                'office_name'        => $salesReport['office_name'],
                'supplier_oper_name' => $salesReport['supplier_oper_name'],
                'order_dt'           => $salesReport['order_dt'],
                'sale_dt'            => $salesReport['sale_dt'],
                'rr_dt'              => $salesReport['rr_dt'],
                'shk_id'             => $salesReport['shk_id'],
                'retail_price_withdisc_rub' => $salesReport['retail_price_withdisc_rub'],
                'delivery_amount'       => $salesReport['delivery_amount'],
                'return_amount'         => $salesReport['return_amount'],
                'delivery_rub'          => $salesReport['delivery_rub'],
                'gi_box_type_name'      => $salesReport['gi_box_type_name'],
                'product_discount_for_report' => $salesReport['product_discount_for_report'],
                'supplier_promo'        => $salesReport['supplier_promo'],
                'rid'                   => $salesReport['rid'],

                'ppvz_spp_prc'          => $salesReport['ppvz_spp_prc'],
                'ppvz_kvw_prc_base'     => $salesReport['ppvz_kvw_prc_base'],
                'ppvz_kvw_prc'          => $salesReport['ppvz_kvw_prc'],
                'ppvz_sales_commission' => $salesReport['ppvz_sales_commission'],
                'ppvz_for_pay'          => $salesReport['ppvz_for_pay'],
                'ppvz_reward'           => $salesReport['ppvz_reward'],
                'acquiring_fee'         => $salesReport['acquiring_fee'] ?? '',
                'acquiring_bank'        => $salesReport['acquiring_bank'] ?? '',
                'ppvz_vw'               => $salesReport['ppvz_vw'],
                'ppvz_vw_nds'           => $salesReport['ppvz_vw_nds'],
                'ppvz_office_id'        => $salesReport['ppvz_office_id'],
                'ppvz_office_name'      => $salesReport['ppvz_office_name'] ?? '',
                'ppvz_supplier_id'      => $salesReport['ppvz_supplier_id'],
                'ppvz_supplier_name'    => $salesReport['ppvz_supplier_name'],
                'ppvz_inn'              => $salesReport['ppvz_inn'],

                'declaration_number' => $salesReport['declaration_number'],
                'bonus_type_name'    => $salesReport['bonus_type_name'] ?? '',
                'sticker_id'         => $salesReport['sticker_id'],
                'site_country'       => $salesReport['site_country'],
                'penalty'            => $salesReport['penalty'],
                'additional_payment' => $salesReport['additional_payment'],
                'srid'               => $salesReport['srid'],
            ],
            $reports
        );

        array_map(
            fn ($wbSalesReportsChunk) =>
                WbSalesReport::query()->upsert($wbSalesReportsChunk, ['rrd_id']),
                array_chunk($wbSalesReports, 1000)
        );
    }
}
