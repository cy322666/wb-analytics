<?php

namespace App\Jobs\WB;

use App\Models\Account;
use App\Models\WB\WbAdvert;
use App\Services\DB\Manager;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Config;

class WbAdvertsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected string $db;

    public int $tries = 1;

    public int $timeout = 30;

    public int $backoff = 10;

    private static array $dictTypes = [
        '4' => 'реклама в каталоге',
        '5' => 'реклама в карточке товара',
        '6' => 'реклама в поиске',
        '7' => 'реклама в рекомендациях на главной странице',
    ];

    private static array $dictStatus = [
        '9'  => 'идут показы',
        '11' => 'РК на паузе',
    ];

    public function tags(): array
    {
        return ['wb:adverts', $this->account->name];
    }

    public function __construct(protected Account $account) {}

    /**
     * @throws \Exception
     */
    public function handle()
    {
        ((new Manager()))->init($this->account);

        $wbApi = (new \App\Services\WB\Wildberries([
            'advert'  => $this->account->token_adv,
        ]));

        /**
         * @TODO:
         * Работает только у клиентов, у которых определен токен 'token_api_adv'
         * или он !== 'NULL'.
         * Чтобы работало для всех клиентов, нужно добавить ключ 'token_api_adv'.
         */

        $today = Carbon::now()->subHours(1)->format('Y-m-d');//TODO

        $adverts = json_decode(
            $wbApi->getAdverts()->getBody()->getContents(), true
        );

        foreach ($adverts as $advert) {

            $detailAdverts[] = json_decode(
                $wbApi->getAdvert(id: $advert['advertId'])
                    ->getBody()
                    ->getContents(), true
            );
        }

        $advertsForSaving = collect(array_map(
            fn ($advert) =>
            array_map(
                fn ($param, $index) => [
                    'date' => $today,
                    'advert_id'   => $advert['advertId'],
                    'type'        => $advert['type'] ?? null,
                    'type_name'   => static::$dictTypes[$advert['type']] ?? 'unknown',
                    'status'      => $advert['status'],
                    'status_name' => static::$dictStatus[$advert['status']] ?? 'unknown',
                    'create_time' => $advert['createTime'] ?? null,
                    'change_time' => $advert['changeTime'] ?? null,
                    'param_index' => $index,
                    'intervals'   => json_encode($param['intervals'] ?? null, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
                    'daily_budget'=> $param['dailyBudget'] ?? null,
                    'price'       => $param['price'] ?? null,
                    'menu_id'     => $param['menuId'] ?? null,
                    'subject_id'  => $param['subjectId'] ?? null,
                    'subject_name'=> $param['subjectName'] ?? null,
                    'set_id'      => $param['setId'] ?? null,
                ],
                $advert['params'],
                array_keys($advert['params'])
            ),
            $detailAdverts
        ))
            ->collapse()
            ->toArray();

//        WbAdvert::query()->where([['account_id', $this->account->id], ['date', $today]])->delete();
        WbAdvert::query()->insert($advertsForSaving);
    }
}
