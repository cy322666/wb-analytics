<?php

namespace App\Models\WB;

use Illuminate\Database\Eloquent\Model;

abstract class SalesReport extends Model
{
    abstract static public function serialize(array $salesReport, string $marker): array;
}
