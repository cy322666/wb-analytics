<?php

namespace App\Models\WB;

class AjaxIncome extends Income
{
    protected $table = 'wb_ajax_incomes';
    protected $fillable = [
        'incomeId','number','date','lastChangeDate','supplierArticle','techSize','barcode','quantity','totalPrice',
        'dateClose','warehouseName','nmId','status'
    ];

    static public function serialize(array $income, string $marker=''): array {
        return $res_income = [
            'incomeId' => $income['incomeId'],
            'number' => $income['number'],
            'date' => $income['date'],
            'lastChangeDate' => $income['lastChangeDate'],
            'supplierArticle' => $income['supplierArticle'],
            'techSize' => $income['techSize'],
            'barcode' => $income['barcode'],
            'quantity' => $income['quantity'],
            'totalPrice' => $income['totalPrice'],
            'dateClose' => $income['dateClose'],
            'warehouseName' => $income['warehouseName'],
            'nmId' => $income['nmId'],
            'status' => $income['status'],
            'dag_date' => \Carbon\Carbon::today()->format('Y-m-d'),
        ];
    }
}
