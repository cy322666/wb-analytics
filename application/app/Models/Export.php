<?php

namespace App\Models;

use App\Jobs\WB\GetOrders;
use App\Jobs\WB\GetStocks;
use App\Jobs\WB\GetSupplies;
use App\Jobs\WB\GetWarehouses;
use App\Models\Account;
use App\Models\WB\Order;
use App\Models\WB\Stock;
use App\Models\WB\Supplies;
use App\Models\WB\Warehouse;
use App\Services\Job\OrderCheck;
use Carbon\Carbon;
use Illuminate\Bus\Batch;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Bus;
use Throwable;

class Export extends Model
{
    use HasFactory;

    protected $table = 'exports';

    protected $connection = 'pgsql';

    protected $fillable = [
        'user_id',
        'account_id',
        'type',
        'start_at',
        'finish_at',
        'status',
    ];

    /**
     * @throws Throwable
     */
    public function reUpdate()
    {
        $jobName = match ($this->type) {
            Stock::class => GetStocks::class,
            Order::class => GetOrders::class,
            Supplies::class  => GetSupplies::class,
            Warehouse::class => GetWarehouses::class,
        };

        $checkerName = match ($this->type) {
//            Stock::class => GetStocks::class,
            Order::class => OrderCheck::class,
//            Supplies::class  => GetSupplies::class,
//            Warehouse::class => GetWarehouses::class,
        };

        $checker = (new $checkerName($this))->check();

        if ($checker->total > 0) {

            $batch = Bus::batch($checker->buildBus($jobName, $this))
                ->then(function (Batch $batch) {
                    // Все задания успешно завершены ...
                    // нотификации
                })->catch(function (Batch $batch, Throwable $e) {
                    // Обнаружено первое проваленное задание из пакета ...
                    // нотификация
                })->finally(function (Batch $batch) {
                    // Завершено выполнение пакета ...
                    // нотификация
                })->dispatch();

        } else {
            $this->finish_at = Carbon::now()->format('Y-m-d H:i:s');
            $this->status = 2;
            $this->save();
        }
//        GetOrders::dispatch($this);//->delay(Carbon::now());
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'account_id', 'id');
    }
}
