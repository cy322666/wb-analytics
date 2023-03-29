<?php

namespace App\Jobs\WB;

use App\Models\Account;
use App\Models\WB\WbOrder;
use App\Services\DB\Manager;
use App\Services\WB\Wildberries;
use Carbon\Carbon;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use Illuminate\Support\Facades\Log;

use function Symfony\Component\String\s;

class WbOrdersJob implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    //лимит попыток
    public int $tries = 1;

    //длительность выполнения
    public int $timeout = 5400;

    //ожидание сек до повтора после фейла
    public int $backoff = 10;

    private static string $defaultDateFrom;

    private static int $countDaysLoading = 7;

    public function __construct(protected Account $account) {}

    public function tags(): array
    {
        return ['wb:orders', $this->account->name];
    }

    /**
     * @throws Exception
     */
    public function handle()
    {
        ((new Manager()))->init($this->account);

        $wbApi = (new Wildberries([
            'standard'  => $this->account->token_standard,
            'statistic' => $this->account->token_statistic,
        ]));

        static::$defaultDateFrom = Carbon::now()->subYears(3)->format('Y-m-d');

        $dateFrom = WbOrder::query()->exists()
            ? Carbon::parse(WbOrder::query()->latest('last_change_date')->value('last_change_date'))->subDays(2)
            : Carbon::parse(static::$defaultDateFrom);

        $ordersResponse = $wbApi->getSupplierOrders($dateFrom);

        $orders = json_decode(
            $ordersResponse->getBody()->getContents(), true
        );

        Log::channel('request')->info('date : '.static::$defaultDateFrom.' => count : '.count($orders));

        $wbOrders = array_map(
            fn ($order) => [
                'date'             => $order['date'],
                'last_change_date' => $order['lastChangeDate'],
                'supplier_article' => $order['supplierArticle'],
                'tech_size'        => $order['techSize'],
                'barcode'          => $order['barcode'],
                'total_price'      => $order['totalPrice'],
                'discount_percent' => $order['discountPercent'],
                'warehouse_name'   => $order['warehouseName'],
                'oblast'           => $order['oblast'],
                'income_id'        => $order['incomeID'],
                'odid'             => $order['odid'],
                'nm_id'     => $order['nmId'],
                'subject'   => $order['subject'],
                'category'  => $order['category'],
                'brand'     => $order['brand'],
                'is_cancel' => $order['isCancel'],
                'cancel_dt' => $order['cancel_dt'],
                'g_number'  => $order['gNumber'],
                'sticker'   => $order['sticker'],
                'srid'      => $order['srid'],
            ],
            $orders
        );

        array_map(
            fn ($wbOrdersChunk) =>
                WbOrder::query()->upsert($wbOrdersChunk, ['odid']),
                array_chunk($wbOrders, 1000)
        );
    }

    //TODO command
    //php artisan queue:clear redis --queue=emails
    //php artisan queue:flush
}
