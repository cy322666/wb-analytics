<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    use HasFactory;

    protected $fillable = [
        'token',
        'is_active',
        'user_id',
        'name',
        'last_updated_at',
        'expired_at',
    ];
}
