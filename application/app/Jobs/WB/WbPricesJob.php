<?php

namespace App\Jobs;

use App\Models\Account;
use App\Models\WbPrice;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Config;
use KFilippovk\Wildberries\Facades\Wildberries;

class WbPricesJob implements ShouldQueue
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

        $items = Wildberries::config($keys)->getInfo();

        $pricesForSave = array_map(
            fn ($item) =>
            [
                'account_id' => $this->account->id,
                'nm_id' => $item->nmId,
                'price' => $item->price,
                'discount' => $item->discount,
                'promo_code' => $item->promoCode,
                'date' => $today
            ],
            $items
        );

        WbPrice::where([['account_id', $this->account->id], ['date', $today]])->delete();
        $pricesForSaveChunks = array_chunk($pricesForSave, 1000);
        array_map(fn ($chunk) => WbPrice::insert($chunk), $pricesForSaveChunks);
    }
}
