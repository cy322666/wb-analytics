<?php

namespace App\Models\WB;

use Illuminate\Database\Eloquent\Model;

abstract class Sale extends Model
{
    abstract static public function serialize(array $sales, string $marker): array;
}
