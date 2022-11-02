<?php

namespace App\Models;

use App\Models\WB\Order;
use App\Models\WB\Stock;
use App\Models\WB\Supplies;
use App\Models\WB\Warehouse;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class Account extends Model
{
    use HasFactory;

    protected $table = 'accounts';

    protected $connection = 'pgsql';

    protected $fillable = [
        'name',
        'token',
        'is_active',
        'user_id',
        'last_updated_at',
        'expired_at',
        'db_port',
        'db_name',
        'db_username',
        'db_password',
        'db_host',
        'db_type',
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

            $result = Artisan::call("wb:install $item->db_name");

            Log::alert(__METHOD__.' : result create db : '.$result);
        });
    }

    public function exports(): HasMany
    {
        return $this->hasMany(Export::class, 'account_id', 'id');
    }
}
