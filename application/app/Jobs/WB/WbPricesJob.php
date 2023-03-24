<?php

namespace App\Jobs\WB;

use App\Models\Account;
use App\Models\WB\WbPrice;
use App\Services\DB\Manager;
use App\Services\WB\Wildberries;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class WbPricesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected string $db;

    public int $tries = 1;

    public int $timeout = 300;

    public int $backoff = 10;

    public function tags(): array
    {
        return ['wb:prices', $this->account->name];
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
        /**
         * @TODO:
         * Работает только у клиентов, у которых определен токен 'token_api'
         * или он !== 'NULL'.
         * Чтобы работало для всех клиентов, нужно добавить ключ 'token_api'.
         */
//        if (!isset($keys['token_api']) || $keys['token_api'] === 'NULL') {
//            return;
//        }

        $today = Carbon::now()->subHours(1)->format('Y-m-d');//TODO

        $pricesResponse = $wbApi->getInfo(0);

        $prices = json_decode(
            $pricesResponse->getBody()->getContents(), true
        );

        $wbPrices = array_map(
            fn ($item) => [
                'nm_id'      => $item['nmId'],
                'price'      => $item['price'],
                'discount'   => $item['discount'],
                'promo_code' => $item['promoCode'],
                'date' => $today
            ],
            $prices
        );

//        WbPrice::where([['account_id', $this->account->id], ['date', $today]])->delete();

        array_map(
            fn ($chunk) =>
                WbPrice::query()->insert($chunk),
                array_chunk($wbPrices, 1000)
        );
    }
}
