<?php

namespace App\Models\WB;

use Illuminate\Database\Eloquent\Model;

abstract class Stock extends Model
{
    abstract static public function serialize(array $stock, string $marker): array;
}
