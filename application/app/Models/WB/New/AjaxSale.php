<?php

namespace App\Models\WB;

class AjaxSale extends Sale
{
    protected $table = 'wb_ajax_sales';
    protected $fillable = [
        'number','date','lastChangeDate','supplierArticle','techSize','barcode','totalPrice','discountPercent','isSupply',
        'isRealization','promoCodeDiscount','warehouseName','countryName','oblastOkrugName','regionName','incomeID',
        'saleID','odid','spp','forPay','finishedPrice','priceWithDisc','nmId','subject','category','brand','IsStorno',
        'gNumber','sticker'
    ];

    static public function serialize(array $sale, string $marker=''): array {
        return $res_sale = [
            'number' => $sale['number'] ?? null,
            'date' => $sale['date'],
            'lastChangeDate' => $sale['lastChangeDate'],
            'supplierArticle' => $sale['supplierArticle'],
            'techSize' => $sale['techSize'],
            'barcode' => $sale['barcode'],
            'quantity' => $sale['quantity'] ?? null,
            'totalPrice' => $sale['totalPrice'],
            'discountPercent' => $sale['discountPercent'],
            'isSupply' => $sale['isSupply'],
            'isRealization' => $sale['isRealization'],
            'orderId' => $sale['orderId'] ?? null,
            'promoCodeDiscount' => $sale['promoCodeDiscount'],
            'warehouseName' => $sale['warehouseName'],
            'countryName' => $sale['countryName'],
            'oblastOkrugName' => $sale['oblastOkrugName'],
            'regionName' => $sale['regionName'],
            'incomeID' => $sale['incomeID'],
            'saleID' => $sale['saleID'],
            'odid' => $sale['odid'],
            'spp' => $sale['spp'],
            'forPay' => $sale['forPay'],
            'finishedPrice' => $sale['finishedPrice'],
            'priceWithDisc' => $sale['priceWithDisc'],
            'nmId' => $sale['nmId'],
            'subject' => $sale['subject'],
            'category' => $sale['category'],
            'brand' => $sale['brand'],
            'IsStorno' => $sale['IsStorno'],
            'gNumber' => $sale['gNumber'],
            'sticker' => $sale['sticker'],
            'dag_date' => \Carbon\Carbon::today()->format('Y-m-d'),
            'srid' => $sale['srid'],
        ];
    }
}
