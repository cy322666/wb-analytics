<?php

namespace App\Jobs;

use App\Models\Account;
use App\Models\WB\WbAdvert;
use App\Models\WB\WbAdvertsCpm;
use App\Services\DB\Manager;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class WbAdvertsCmpJob implements ShouldQueue
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

    private static array $associationParamColumnAndType = [
        'menu_id'    => '4',
        'set_id'     => '5',
        'subject_id' => '6',
    ];

    public function uniqueId(): string
    {
        return 'advertcmp-account-'.$this->account->id;
    }

    public function __construct(protected Account $account) {}

    /**
     * Execute the job.
     *
     * @return void
     * @throws \Exception
     */
    public function handle()
    {
        ((new Manager()))->init($this->account);

        $wbApi = (new \App\Services\WB\Wildberries([
            'standard'  => $this->account->token_standard,
            'statistic' => $this->account->token_statistic,
        ]));

        /**
         * @TODO:
         * Работает только у клиентов, у которых определен токен 'token_api_adv'
         * или он !== 'NULL'.
         * Чтобы работало для всех клиентов, нужно добавить ключ 'token_api_adv'.
         */
        $parsedAdverts = WbAdvert::query()
            ->get(array_keys(static::$associationParamColumnAndType))
            ->map(
                fn ($item) => [
                    'menu_id' => $item->menu_id,
                    'set_id'  => $item->set_id,
                    'subject_id' => $item->subject_id,
                ]
            );
        $paramsMenuId    = $parsedAdverts->pluck('menu_id')->unique()->reject(fn ($item) => $item === null)->values()->toArray();
        $paramsSetId     = $parsedAdverts->pluck('set_id')->unique()->reject(fn ($item) => $item === null)->values()->toArray();
        $paramsSubjectId = $parsedAdverts->pluck('subject_id')->unique()->reject(fn ($item) => $item === null)->values()->toArray();

        $today = Carbon::now()->subHours(1)->format('Y-m-d');//TODO

        $advertsCpmForSave = [];
        foreach ($paramsMenuId as $param) {
            $responseData = $wbApi->getCpm(type: static::$associationParamColumnAndType['menu_id'], param: $param);
            $advertsCpmForSave = array_merge(
                array_map(
                    fn ($cpm) => [
                        'date' => $today,
                        'type' => static::$associationParamColumnAndType['menu_id'],
                        'type_name' =>  static::$dictTypes[ static::$associationParamColumnAndType['menu_id']],
                        'param' => $param,
                        'cmp'   => $cpm['Cpm'] ?? null,
                        'count' => $cpm['Count'] ?? null,
                    ],
                    $responseData
                ),
                $advertsCpmForSave
            );
        }
        foreach ($paramsSetId as $param) {
            $responseData = $wbApi->getCpm(type: static::$associationParamColumnAndType['set_id'], param: $param);
            $advertsCpmForSave = array_merge(
                array_map(
                    fn ($cpm) => [
                        'date' => $today,
                        'type' => static::$associationParamColumnAndType['set_id'],
                        'type_name' => static::$dictTypes[static::$associationParamColumnAndType['set_id']],
                        'param' => $param,
                        'cmp'   => $cpm['Cpm'] ?? null,
                        'count' => $cpm['Count'] ?? null,
                    ],
                    $responseData
                ),
                $advertsCpmForSave
            );
        }
        foreach ($paramsSubjectId as $param) {
            $responseData = $wbApi->getCpm(type: static::$associationParamColumnAndType['subject_id'], param: $param);
            $advertsCpmForSave = array_merge(
                array_map(
                    fn ($cpm) => [
                        'date' => $today,
                        'type' => static::$associationParamColumnAndType['subject_id'],
                        'type_name' => static::$dictTypes[static::$associationParamColumnAndType['subject_id']],
                        'param' => $param,
                        'cmp'   => $cpm['Cpm'] ?? null,
                        'count' => $cpm['Count'] ?? null,
                    ],
                    $responseData
                ),
                $advertsCpmForSave
            );
        }

//        WbAdvertsCpm::where([['account_id', $this->account->id], ['date', $today]])->delete();

        array_map(
            fn ($chunk) =>
            WbAdvertsCpm::query()->insert($chunk),
            array_chunk($advertsCpmForSave, 1000)
        );
    }
}
