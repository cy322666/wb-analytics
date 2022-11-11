<?php

namespace App\Models\WB\Supplier;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    use HasFactory;

    protected $table = 'wb_supplier_stocks';

    protected $fillable = [
        'last_change_date',
        'supplier_article',
        'tech_size',
        'barcode',
        'quantity',
        'is_supply',
        'is_realization',
        'quantity_full',
        'quantity_not_in_orders',
        'warehouse',
        'warehouse_name',
        'in_way_to_client',
        'in_way_from_client',
        'nm_id',
        'subject',
        'category',
        'days_on_site',
        'brand',
        'scc_ode',
        'price',
        'discount',
    ];
}
