<?php

namespace App\Jobs;

use App\Models\Account;
use App\Models\WbAdvert;
use App\Models\WbAdvertsCpm;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Config;
use KFilippovk\Wildberries\Facades\WildberriesAdvert;

class WbAdvertsCmpJob implements ShouldQueue
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

        $associationParamColumnAndType = [
            'menu_id' => '4',
            'set_id' => '5',
            'subject_id' => '6',
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

        $parsedAdverts = WbAdvert::where('account_id', $this->account->id)
            ->get(array_keys($associationParamColumnAndType))
            ->map(
                fn ($item) =>
                [
                    'menu_id' => $item->menu_id,
                    'set_id' => $item->set_id,
                    'subject_id' => $item->subject_id,
                ]
            );
        $paramsMenuId = $parsedAdverts->pluck('menu_id')->unique()->reject(fn ($item) => $item === null)->values()->toArray();
        $paramsSetId = $parsedAdverts->pluck('set_id')->unique()->reject(fn ($item) => $item === null)->values()->toArray();
        $paramsSubjectId = $parsedAdverts->pluck('subject_id')->unique()->reject(fn ($item) => $item === null)->values()->toArray();

        $today = Carbon::now()->subHours($hourStartDay)->format('Y-m-d');

        $advertsCpmForSave = [];
        foreach ($paramsMenuId as $param) {
            $responseData = WildberriesAdvert::config($keys)->getCpm(type: $associationParamColumnAndType['menu_id'], param: $param);
            $advertsCpmForSave = array_merge(
                array_map(
                    fn ($cpm) =>
                    [
                        'account_id' => $this->account->id,
                        'date' => $today,
                        'type' => $associationParamColumnAndType['menu_id'],
                        'type_name' => $dictTypes[$associationParamColumnAndType['menu_id']],
                        'param' => $param,
                        'cmp' => $cpm->Cpm ?? null,
                        'count' => $cpm->Count ?? null,
                    ],
                    $responseData
                ),
                $advertsCpmForSave
            );
        }
        foreach ($paramsSetId as $param) {
            $responseData = WildberriesAdvert::config($keys)->getCpm(type: $associationParamColumnAndType['set_id'], param: $param);
            $advertsCpmForSave = array_merge(
                array_map(
                    fn ($cpm) =>
                    [
                        'account_id' => $this->account->id,
                        'date' => $today,
                        'type' => $associationParamColumnAndType['set_id'],
                        'type_name' => $dictTypes[$associationParamColumnAndType['set_id']],
                        'param' => $param,
                        'cmp' => $cpm->Cpm ?? null,
                        'count' => $cpm->Count ?? null,
                    ],
                    $responseData
                ),
                $advertsCpmForSave
            );
        }
        foreach ($paramsSubjectId as $param) {
            $responseData = WildberriesAdvert::config($keys)->getCpm(type: $associationParamColumnAndType['subject_id'], param: $param);
            $advertsCpmForSave = array_merge(
                array_map(
                    fn ($cpm) =>
                    [
                        'account_id' => $this->account->id,
                        'date' => $today,
                        'type' => $associationParamColumnAndType['subject_id'],
                        'type_name' => $dictTypes[$associationParamColumnAndType['subject_id']],
                        'param' => $param,
                        'cmp' => $cpm->Cpm ?? null,
                        'count' => $cpm->Count ?? null,
                    ],
                    $responseData
                ),
                $advertsCpmForSave
            );
        }

        WbAdvertsCpm::where([['account_id', $this->account->id], ['date', $today]])->delete();
        $advertsCpmForSaveChunks = array_chunk($advertsCpmForSave, 1000);
        array_map(fn ($chunk) => WbAdvertsCpm::insert($chunk), $advertsCpmForSaveChunks);
    }
}
