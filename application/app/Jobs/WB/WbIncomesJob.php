<?php

namespace App\Jobs\WB;

use App\Models\Account;
use App\Models\WB\WbIncome;
use App\Services\DB\Manager;
use App\Services\WB\Wildberries;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class WbIncomesJob implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 1;

    public int $timeout = 30;

    public int $backoff = 10;

    private static string $defaultDateFrom = '2022-02-13';
    private static int $countDaysLoading = 5;

    public function tags(): array
    {
        return ['wb:incomes', $this->account->name];
    }

    public function __construct(protected Account $account) {}

    public function handle()
    {
        ((new Manager()))->init($this->account);

        $wbApi = (new Wildberries([
            'standard'  => $this->account->token_standard,
            'statistic' => $this->account->token_statistic,
        ]));


        $dateFrom = WbIncome::query()->exists()
            ? Carbon::parse(WbIncome::query()->latest()->first()->date)->subDays(2)
            : Carbon::parse(static::$defaultDateFrom);

        do {
            $incomesResponse = $wbApi->getSupplierIncomes($dateFrom);

            $incomes = json_decode(
                $incomesResponse->getBody()->getContents(), true
            );

            $wbIncomes = array_map(
                fn ($income) => [
                    'income_id' => $income['incomeId'],
                    'number'    => $income['number'],
                    'date'      => $income['date'],
                    'last_change_date' => $income['lastChangeDate'],
                    'supplier_article' => $income['supplierArticle'],
                    'tech_size' =>   $income['techSize'],
                    'barcode' =>     $income['barcode'],
                    'quantity' =>    $income['quantity'],
                    'total_price' => $income['totalPrice'],
                    'date_close' =>  $income['dateClose'],
                    'warehouse_name' => $income['warehouseName'],
                    'nm_id'  => $income['nmId'],
                    'status' => $income['status'],
                ],
                $incomes
            );

            array_map(
                fn ($chunk) =>
                    WbIncome::query()->upsert($chunk, ['income_id', 'date', 'last_change_date', 'barcode', 'status']),
                    array_chunk($wbIncomes, 1000)
            );
        } while (count($incomes) >= 100_000);
    }
}
