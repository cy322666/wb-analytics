<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Account extends Model
{
    use HasFactory;

    protected $table = 'accounts';

    protected $connection = 'pgsql';

    protected $fillable = [
        'name',
        'token_standard',
        'token_statistic',
        'token_adv',
        'is_active',
        'user_id',
        'is_remote',
        'last_updated_at',
        'expired_at',
        'db_port',
        'db_name',
        'db_username',
        'db_password',
        'db_host',
        'db_type',
        'time_load',//время выгрузки
    ];

    public static array $commandsWB = [
        'wb:orders',
        'wb:incomes',
        'wb:prices',
        'wb:sales',
        'wb:stocks',

        'wb:sale-reports'
    ];

    public function addTasksWB()
    {
        foreach (static::$commandsWB as $command) {

            Task::query()->create([
                'user_id'    => $this->user->id,
                'account_id' => $this->id,
                'command'    => $command,
                'params'     => json_encode(['time' => $this->time_load])
            ]);
        }
    }

    public function reloadTask(Task $task)
    {
        $task->is_active = true;
        $task->completed = true;
        $task->status = 0;
        $task->uuid = null;
        $task->save();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class, 'account_id', 'id');
    }
}
