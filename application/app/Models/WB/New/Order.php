<?php

namespace App\Models\WB;

use Illuminate\Database\Eloquent\Model;

abstract class Order extends Model
{
    abstract static public function serialize(array $order, string $marker): array;
}
