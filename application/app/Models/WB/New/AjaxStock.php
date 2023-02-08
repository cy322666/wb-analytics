<?php

namespace App\Models\WB;

class AjaxStock extends Stock
{
    protected $table = 'wb_ajax_stocks';
    protected $fillable = [
        'lastChangeDate','supplierArticle','techSize','barcode','quantity','isSupply','isRealization','quantityFull',
        'quantityNotInOrders','warehouse','warehouseName','inWayToClient','inWayFromClient','nmId','subject','category',
        'daysOnSite','brand','SCCode','Price','Discount'
    ];

    static public function serialize(array $stock, string $marker=''): array {
        return $res_stock = [
            'lastChangeDate' => $stock['lastChangeDate'],
            'supplierArticle' => $stock['supplierArticle'],
            'techSize' => $stock['techSize'],
            'barcode' => $stock['barcode'],
            'quantity' => $stock['quantity'],
            'isSupply' => $stock['isSupply'],
            'isRealization' => $stock['isRealization'],
            'quantityFull' => $stock['quantityFull'],
            'quantityNotInOrders' => $stock['quantityNotInOrders'],
            'warehouse' => $stock['warehouse'],
            'warehouseName' => $stock['warehouseName'],
            'inWayToClient' => $stock['inWayToClient'],
            'inWayFromClient' => $stock['inWayFromClient'],
            'nmId' => $stock['nmId'],
            'subject' => $stock['subject'],
            'category' => $stock['category'],
            'daysOnSite' => $stock['daysOnSite'],
            'brand' => $stock['brand'],
            'SCCode' => $stock['SCCode'],
            'Price' => $stock['Price'],
            'Discount' => $stock['Discount'],
            'dag_date' => \Carbon\Carbon::today()->format('Y-m-d')
        ];
    }
}
