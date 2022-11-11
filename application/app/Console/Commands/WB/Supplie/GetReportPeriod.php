<?php

namespace App\Console\Commands\WB\Supplie;

use App\Models\Export;
use App\Models\WB\Supplier\Income;
use App\Models\WB\Supplier\Order;
use App\Models\WB\Supplier\ReportPeriod;
use App\Services\DB\Manager;
use App\Services\WB\Models\Supplie\Incomes;
use App\Services\WB\RequestDto;
use App\Services\WB\ResponseParser;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Console\Command;
use App\Services\WB\Client as WB;
use Illuminate\Support\Facades\Log;
use Laravel\Octane\Exceptions\DdException;
use Symfony\Component\Console\Command\Command as CommandAlias;
use Throwable;

class GetReportPeriod extends Command
{
    protected $signature = 'wb:supplie_report_period {export}';

    protected $description = 'Command description';

    /**
     * @throws GuzzleException|DdException
     */
    public function handle(): int
    {
        $export  = Export::find($this->argument('export'));
        $account = $export->account;;
        $options = json_decode($export->options);

        $dbManager = (new Manager());
        $dbManager->init($account);

        $wb = WB::init(
            $account->token,
            $account->token32,
            $account->token64
        );

        $request = (new RequestDto());
        $request->dateFrom = '2022-11-05';
        $request->dateTo = '2022-11-06';
        $request->limit = 10000;
        //rrd_id
//        $request->end  = '';
//        $request->take = '';
//        $request->skip = '';

        try {
            $response = $wb->reportPeriod('supplie')->all($request);

            $response = (new ResponseParser)->parse($response);

            foreach ($response as $report) {

                ReportPeriod::query()->create([
                    'realizationreport_id' => $report->realizationreport_id,
                    'suppliercontract_code' => $report->suppliercontract_code,
                    'rrd_id' => $report->rrd_id,
                    'gi_id' => $report->gi_id,
                    'subject_name' => $report->subject_name,
                    'nm_id' => $report->nm_id,
                    'brand_name' => $report->brand_name,
                    'sa_name' => $report->sa_name,
                    'ts_name' => $report->ts_name,
                    'barcode' => $report->barcode,
                    'doc_type_name' => $report->doc_type_name,
                    'quantity' => $report->quantity,
                    'retail_price' => $report->retail_price,
                    'retail_amount' => $report->retail_amount,
                    'sale_percent' => $report->sale_percent,
                    'commission_percent' => $report->commission_percent,
                    'office_name' => $report->office_name,
                    'supplier_oper_name' => $report->supplier_oper_name,
                    'order_dt' => $report->order_dt,
                    'sale_dt' => $report->sale_dt,
                    'rr_dt' => $report->rr_dt,
                    'shk_id' => $report->shk_id,
                    'retail_price_withdisc_rub' => $report->retail_price_withdisc_rub,
                    'delivery_amount' => $report->delivery_amount,
                    'return_amount' => $report->return_amount,
                    'delivery_rub' => $report->delivery_rub,
                    'gi_box_type_name' => $report->gi_box_type_name,
                    'product_discount_for_report' => $report->product_discount_for_report,
                    'supplier_promo' => $report->supplier_promo,
                    'rid' => $report->rid,
                    'ppvz_spp_prc' => $report->ppvz_spp_prc,
                    'ppvz_kvw_prc_base' => $report->ppvz_kvw_prc_base,
                    'ppvz_kvw_prc' => $report->ppvz_kvw_prc,
                    'ppvz_sales_commission' => $report->ppvz_sales_commission,
                    'ppvz_for_pay' => $report->ppvz_for_pay,
                    'ppvz_reward' => $report->ppvz_reward,
                    'ppvz_vw' => $report->ppvz_vw,
                    'ppvz_vw_nds' => $report->ppvz_vw_nds,
                    'ppvz_office_id' => $report->ppvz_office_id,
                    'ppvz_office_name' => $report->ppvz_office_name,
                    'ppvz_supplier_id' => $report->ppvz_supplier_id,
                    'ppvz_supplier_name' => $report->ppvz_supplier_name,
                    'ppvz_inn' => $report->ppvz_inn,
                    'declaration_number' => $report->declaration_number,
                    'sticker_id' => $report->sticker_id,
                    'site_country' => $report->site_country,
                    'penalty' => $report->penalty,
                    'additional_payment' => $report->additional_payment,
                ]);
            }

            return CommandAlias::SUCCESS;

        } catch (Throwable $exception) {
dd($exception->getMessage());
            Log::alert(__METHOD__.' : '.$exception->getMessage());

            return CommandAlias::FAILURE;
        }
    }
}
