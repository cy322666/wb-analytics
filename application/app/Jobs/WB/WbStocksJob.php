<?php

namespace App\Jobs;

use App\Models\Account;
use App\Models\WbStock;
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

class WbStocksJob implements ShouldQueue
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
        $hourStartDay = Config::get('time.hour_start_day');

        $maxRetryRequests = 30;

        $countSleep = 2;
        $currentRetryRequests = 0;

        // DEBUG
        dump('account id: ' . $this->account->id);

        $dateFrom = Carbon::parse('2022-01-01');

        $keys = $this->account->getIntegrationKeysMap();

        $stocks = null;
        while ($currentRetryRequests < $maxRetryRequests) {
            try {
                $currentRetryRequests++;
                $stocks = Wildberries::config($keys)->getSupplierStocks($dateFrom);
                break;
            } catch (Throwable $throwable) {
                if ($throwable instanceof WildberriesException) {
                    dump('WB Exception: Message: '
                        . substr($throwable->getMessage(), 0, 255) . "...\n"
                        . "Sleeping on {$countSleep} seconds ...");
                    sleep($countSleep);
                }
            }
        }

        if ($currentRetryRequests === $maxRetryRequests) {
            throw new Exception("Error: The limit of retry {$maxRetryRequests} has been reached. Stopping send request.");
        }

        $today = Carbon::now()->subHours($hourStartDay)->format('Y-m-d');

        $wbStocks = array_map(
            fn ($stock) =>
            [
                'account_id' => $this->account->id,
                'last_change_date' => $stock->lastChangeDate,
                'supplier_article' => $stock->supplierArticle,
                'tech_size' => $stock->techSize,
                'barcode' => $stock->barcode,
                'quantity' => $stock->quantity,
                'is_supply' => $stock->isSupply,
                'is_realization' => $stock->isRealization,
                'quantity_full' => $stock->quantityFull ?? null,
                'quantity_not_in_orders' => $stock->quantityNotInOrders ?? null,
                'warehouse' => $stock->warehouse ?? null,
                'warehouse_name' => $stock->warehouseName,
                'in_way_to_client' => $stock->inWayToClient ?? null,
                'in_way_from_client' => $stock->inWayFromClient ?? null,
                'nm_id' => $stock->nmId,
                'subject' => $stock->subject,
                'category' => $stock->category,
                'days_on_site' => $stock->daysOnSite,
                'brand' => $stock->brand,
                'sc_code' => $stock->SCCode,
                'price' => $stock->Price,
                'discount' => $stock->Discount,
                'date' => $today
            ],
            $stocks
        );

        WbStock::where([['account_id', $this->account->id], ['date', $today], ['is_supplier_stock', false]])->delete();
        $wbStocksChunks = array_chunk($wbStocks, 1000);
        array_map(fn ($chunk) => WbStock::insert($chunk), $wbStocksChunks);
    }
}
