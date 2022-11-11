<?php

namespace App\Console\Commands\WB\Supplie;

use App\Models\Export;
use App\Models\WB\Supplier\Income;
use App\Models\WB\Supplier\Order;
use App\Models\WB\Supplier\ReportExcise;
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

class GetReportExcise extends Command
{
    protected $signature = 'wb:supplie_report_excise {export}';

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
        $request->dateFrom = '2022-01-01';
        $request->flag = 0;
//        $request->end  = '';
//        $request->take = '';
//        $request->skip = '';

        try {
            $response = $wb->reportExcise('supplie')->all($request);

            $response = (new ResponseParser)->parse($response);

            foreach ($response as $report) {
                ReportExcise::query()->create([
                    'report_id' => $report->id,
                    'finished_price' => $report->finishedPrice,
                    'operation_type_id' => $report->operationTypeId,
                    'fiscal_dt' => $report->fiscalDt,
                    'doc_number' => $report->docNumber,
                    'fn_number' => $report->fnNumber,
                    'reg_number' => $report->regNumber,
                    'excise' => $report->excise,
                    'date' => $report->date,
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
