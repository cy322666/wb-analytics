<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $connection = 'default';

    protected $fillable = [
        'user_id',
        'account_id',
        'command',
        'params',
        'is_active',
        'completed',
        'status',
        'uuid',
    ];
}
