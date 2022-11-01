<?php

namespace App\Models;

use App\Models\WB\Export;
use App\Models\WB\Order;
use App\Models\WB\Stock;
use App\Models\WB\Supplies;
use App\Models\WB\Warehouse;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Account extends Model
{
    use HasFactory;

    protected $table = 'accounts';

    protected $fillable = [
        'token',
        'is_active',
        'user_id',
        'name',
        'last_updated_at',
        'expired_at',
        'ip',
        'user',
        'pass',
        'port',
        'driver',
    ];

    protected static function boot()
    {
        parent::boot();

        static::created(function($item) {

            Export::query()->create([
                'user_id'  => $item->user_id,
                'type'     => Stock::class,
                'start_at' => Carbon::now(),
                'account_id' => $item->id,
            ]);

            Export::query()->create([
                'user_id'  => $item->user_id,
                'type'     => Warehouse::class,
                'start_at' => Carbon::now(),
                'account_id' => $item->id,
            ]);

            Export::query()->create([
                'user_id'  => $item->user_id,
                'type'     => Order::class,
                'start_at' => Carbon::now(),
                'account_id' => $item->id,
            ]);

            Export::query()->create([
                'user_id'  => $item->user_id,
                'type'     => Supplies::class,
                'start_at' => Carbon::now(),
                'account_id' => $item->id,
            ]);
        });
    }

    public function exports(): HasMany
    {
        return $this->hasMany(Export::class, 'account_id', 'id');
    }
}
