<?php

namespace App\Models\WB;

class AjaxOrder extends Order
{
    protected $table = 'wb_ajax_orders';
    protected $fillable = [
        'number', 'date','lastChangeDate','supplierArticle','techSize','barcode','quantity','totalPrice','discountPercent',
        'warehouseName','oblast','incomeID','odid','nmId','subject','category','brand','isCancel','cancel_dt','gNumber',
        'sticker','srid'
    ];

    static public function serialize(array $order, string $marker=''): array {
        return $res_order = [
            'number' => $order['number'] ?? null,
            'date' => $order['date'],
            'lastChangeDate' => $order['lastChangeDate'],
            'supplierArticle' => $order['supplierArticle'],
            'techSize' => $order['techSize'],
            'barcode' => $order['barcode'],
            'quantity' => $order['quantity'] ?? null,
            'totalPrice' => $order['totalPrice'],
            'discountPercent' => $order['discountPercent'],
            'warehouseName' => $order['warehouseName'],
            'oblast' => $order['oblast'],
            'incomeID' => $order['incomeID'],
            'odid' => $order['odid'],
            'nmId' => $order['nmId'],
            'subject' => $order['subject'],
            'category' => $order['category'],
            'brand' => $order['brand'],
            'isCancel' => $order['isCancel'],
            'cancel_dt' => $order['cancel_dt'],
            'gNumber' => $order['gNumber'],
            'sticker' => $order['sticker'],
            'dag_date' => \Carbon\Carbon::today()->format('Y-m-d'),
            'srid' => $order['srid']
        ];
    }
}
