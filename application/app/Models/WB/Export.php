<?php

namespace App\Models\WB;

use App\Models\Account;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Export extends Model
{
    use HasFactory;

    protected $table = 'wb_exports';

    protected $fillable = [
        'user_id',
        'account_id',
        'type',
        'start_at',
        'finish_at',
        'status',
    ];

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'account_id', 'id');
    }
}
