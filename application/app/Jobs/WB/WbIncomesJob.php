<?php

namespace App\Jobs;

use App\Models\Account;
use App\Models\WbIncome;
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

class WbIncomesJob implements ShouldQueue
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

        // DEBUG
        dump('account id: ' . $this->account->id);

        $countSleep = 2;
        $currentRetryRequests = 0;
        $maxRetryRequests = 30;

        $countSubtractionMonth = 3;
        $dateFrom = WbIncome::where('account_id', $this->account->id)->count()
            ? Carbon::today()->subMonths($countSubtractionMonth)
            : Carbon::parse('2022-01-01');

        $keys = $this->account->getIntegrationKeysMap();

        $incomes = null;
        while ($currentRetryRequests < $maxRetryRequests) {
            try {
                $currentRetryRequests++;
                $incomes = Wildberries::config($keys)->getSupplierIncomes($dateFrom);
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

        $wbIncomes = array_map(
            fn ($income) =>
            [
                'account_id' => $this->account->id,
                'income_id' => $income->incomeId,
                'number' => $income->number,
                'date' => $income->date,
                'last_change_date' => $income->lastChangeDate,
                'supplier_article' => $income->supplierArticle,
                'tech_size' => $income->techSize,
                'barcode' => $income->barcode,
                'quantity' => $income->quantity,
                'total_price' => $income->totalPrice,
                'date_close' => $income->dateClose,
                'warehouse_name' => $income->warehouseName,
                'nm_id' => $income->nmId,
                'status' => $income->status,
            ],
            $incomes
        );

        $wbIncomesChunks = array_chunk($wbIncomes, 1000);
        array_map(
            fn ($chunk) =>
            WbIncome::upsert($chunk, ['account_id', 'income_id', 'date', 'last_change_date', 'barcode', 'status']),
            $wbIncomesChunks
        );
    }
}
