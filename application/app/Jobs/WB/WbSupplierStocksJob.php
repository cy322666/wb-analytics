<?php

namespace App\Jobs;

use App\Models\Account;
use App\Models\WbStock;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Config;
use KFilippovk\Wildberries\Facades\Wildberries;

class WbSupplierStocksJob implements ShouldQueue
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

        // DEBUG
        dump('account id: ' . $this->account->id);

        $keys = $this->account->getIntegrationKeysMap();

        /**
         * @TODO: 
         * Работает только у клиентов, у которых определен токен 'token_api'
         * или он !== 'NULL'.
         * Чтобы работало для всех клиентов, нужно добавить ключ 'token_api'.
         */
        if (!isset($keys['token_api']) || $keys['token_api'] === 'NULL') {
            return;
        }

        $today = Carbon::now()->subHours($hourStartDay)->format('Y-m-d');

        $dbWBStocks = WbStock::where('account_id', $this->account->id)
            ->orderBy('date', 'DESC')
            ->get()
            ->groupBy('barcode')
            ->map(fn ($stock) => $stock[0])
            ->toArray();

        $skip = 0;
        $take = 1000;

        $supplierStocks = [];

        do {
            $result = Wildberries::config($keys)->getStocks($skip, $take);

            if ($result->stocks === null) {
                break;
            }

            $supplierStocks = array_merge(
                $supplierStocks,
                array_map(
                    fn ($stock) =>
                    [
                        'account_id' => $this->account->id,
                        'last_change_date' => null,
                        'supplier_article' => $stock->article,
                        'tech_size' => $stock->size,
                        'barcode' => $stock->barcode,
                        'quantity' => $stock->stock,
                        'is_supply' => null,
                        'is_realization' => null,
                        'quantity_full' => null,
                        'quantity_not_in_orders' => null,
                        'warehouse' => $stock->warehouseId,
                        'warehouse_name' => $stock->warehouseName,
                        'in_way_to_client' => null,
                        'in_way_from_client' => null,
                        'nm_id' => $stock->nmId,
                        'subject' => $stock->subject,
                        'category' => $dbWBStocks[$stock->barcode]['category'] ?? null,
                        'days_on_site' => null,
                        'brand' => $stock->brand,
                        'sc_code' => null,
                        'price' => $dbWBStocks[$stock->barcode]['price'] ?? null,
                        'discount' => $dbWBStocks[$stock->barcode]['discount'] ?? null,
                        'is_supplier_stock' => true,
                        'date' => $today
                    ],
                    $result->stocks
                )
            );

            $skip += count($result->stocks);
        } while ($skip < $result->total);

        WbStock::where([['account_id', $this->account->id], ['date', $today], ['is_supplier_stock', true]])->delete();
        $supplierStocksChunks = array_chunk($supplierStocks, 1000);
        array_map(fn ($chunk) => WbStock::insert($chunk), $supplierStocksChunks);
    }
}
