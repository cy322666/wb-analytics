<?php

namespace App\Jobs;

use App\Models\Account;
use App\Models\WbAdvert;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Config;
use KFilippovk\Wildberries\Facades\WildberriesAdvert;

class WbAdvertsJob implements ShouldQueue
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

        $dictTypes = [
            '4' => 'реклама в каталоге',
            '5' => 'реклама в карточке товара',
            '6' => 'реклама в поиске',
            '7' => 'реклама в рекомендациях на главной странице',
        ];

        $dictStatus = [
            '9' => 'идут показы',
            '11' => 'РК на паузе',
        ];
        
        // DEBUG
        dump('account id: ' . $this->account->id);

        $keys = $this->account->getIntegrationKeysMap();

        /**
         * @TODO: 
         * Работает только у клиентов, у которых определен токен 'token_api_adv'
         * или он !== 'NULL'.
         * Чтобы работало для всех клиентов, нужно добавить ключ 'token_api_adv'.
         */
        if (!isset($keys['token_api_adv']) || $keys['token_api_adv'] === 'NULL') {
            return;
        }

        $today = Carbon::now()->subHours($hourStartDay)->format('Y-m-d');

        $responseData = WildberriesAdvert::config($keys)->getAdverts();
        $advertIds = array_column($responseData, 'advertId');

        $adverts = [];
        foreach ($advertIds as $id) {
            $adverts[] = WildberriesAdvert::config($keys)->getAdvert(id: $id);
        }

        $advertsForSaving = collect(array_map(
            fn ($advert) =>
            array_map(
                fn ($param, $index) =>
                [
                    'account_id' => $this->account->id,
                    'date' => $today,
                    'advert_id' => $advert->advertId,
                    'type' => $advert->type ?? null,
                    'type_name' => $dictTypes[$advert->type ?? null] ?? 'unknown',
                    'status' => $advert->status,
                    'status_name' => $dictStatus[$advert->status ?? null] ?? 'unknown',
                    'create_time' => $advert->createTime ?? null,
                    'change_time' => $advert->changeTime ?? null,
                    'param_index' => $index,
                    'intervals' => json_encode($param->intervals ?? null, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
                    'daily_budget' => $param->dailyBudget ?? null,
                    'price' => $param->price ?? null,
                    'menu_id' => $param->menuId ?? null,
                    'subject_id' => $param->subjectId ?? null,
                    'subject_name' => $param->subjectName ?? null,
                    'set_id' => $param->setId ?? null,
                ],
                $advert->params,
                array_keys($advert->params)
            ),
            $adverts
        ))
            ->collapse()
            ->toArray();

        WbAdvert::where([['account_id', $this->account->id], ['date', $today]])->delete();
        WbAdvert::insert($advertsForSaving);
    }
}
