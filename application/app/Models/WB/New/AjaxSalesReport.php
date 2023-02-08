<?php

namespace App\Models\WB;

class AjaxSalesReport extends SalesReport
{
    protected $table = 'wb_ajax_sales_reports';
    protected $fillable = [
        'realizationreport_id','suppliercontract_code','rrd_id','gi_id','subject_name','nm_id','brand_name','sa_name',
        'ts_name','barcode','doc_type_name','quantity','retail_price','retail_amount','sale_percent','commission_percent',
        'office_name','supplier_oper_name','order_dt','sale_dt','rr_dt','shk_id','retail_price_withdisc_rub',
        'delivery_amount','return_amount','delivery_rub','gi_box_type_name','product_discount_for_report','supplier_promo',
        'rid','ppvz_spp_prc','ppvz_kvw_prc_base','ppvz_kvw_prc','ppvz_sales_commission','ppvz_for_pay','ppvz_reward',
        'ppvz_vw','ppvz_vw_nds','ppvz_office_id','ppvz_office_name','ppvz_supplier_id','ppvz_supplier_name','ppvz_inn',
        'bonus_type_name','dag_date','declaration_number','sticker_id','site_country','penalty','additional_payment'
    ];

    static public function serialize(array $salesReport, string $marker=''): array {
        return $res_salesReport = [
            'realizationreport_id' => $salesReport['realizationreport_id'],
            'suppliercontract_code' => $salesReport['suppliercontract_code'],
            'rrd_id' => $salesReport['rrd_id'],
            'gi_id' => $salesReport['gi_id'],
            'subject_name' => $salesReport['subject_name'],
            'nm_id' => $salesReport['nm_id'],
            'brand_name' => $salesReport['brand_name'],
            'sa_name' => $salesReport['sa_name'],
            'ts_name' => $salesReport['ts_name'],
            'barcode' => $salesReport['barcode'],
            'doc_type_name' => $salesReport['doc_type_name'],
            'quantity' => $salesReport['quantity'],
            'retail_price' => $salesReport['retail_price'],
            'retail_amount' => $salesReport['retail_amount'],
            'sale_percent' => $salesReport['sale_percent'],
            'commission_percent' => $salesReport['commission_percent'],
            'office_name' => $salesReport['office_name'] ?? null,
            'supplier_oper_name' => $salesReport['supplier_oper_name'],
            'order_dt' => $salesReport['order_dt'],
            'sale_dt' => $salesReport['sale_dt'],
            'rr_dt' => $salesReport['rr_dt'],
            'shk_id' => $salesReport['shk_id'],
            'retail_price_withdisc_rub' => $salesReport['retail_price_withdisc_rub'],
            'delivery_amount' => $salesReport['delivery_amount'],
            'return_amount' => $salesReport['return_amount'],
            'delivery_rub' => $salesReport['delivery_rub'],
            'gi_box_type_name' => $salesReport['gi_box_type_name'],
            'product_discount_for_report' => $salesReport['product_discount_for_report'],
            'supplier_promo' => $salesReport['supplier_promo'],
            'rid' => $salesReport['rid'],
            'ppvz_spp_prc' => $salesReport['ppvz_spp_prc'],
            'ppvz_kvw_prc_base' => $salesReport['ppvz_kvw_prc_base'],
            'ppvz_kvw_prc' => $salesReport['ppvz_kvw_prc'],
            'ppvz_sales_commission' => $salesReport['ppvz_sales_commission'],
            'ppvz_for_pay' => $salesReport['ppvz_for_pay'],
            'ppvz_reward' => $salesReport['ppvz_reward'],
            'ppvz_vw' => $salesReport['ppvz_vw'],
            'ppvz_vw_nds' => $salesReport['ppvz_vw_nds'],
            'ppvz_office_id' => $salesReport['ppvz_office_id'],
            'ppvz_office_name' => $salesReport['ppvz_office_name'] ?? null,
            'ppvz_supplier_id' => $salesReport['ppvz_supplier_id'],
            'declaration_number' => $salesReport['declaration_number'],
            'sticker_id' => $salesReport['sticker_id'],
            'ppvz_supplier_name' => $salesReport['ppvz_supplier_name'],
            'ppvz_inn' => $salesReport['ppvz_inn'],
            'bonus_type_name' =>$salesReport['bonus_type_name'] ?? null,
            'dag_date' => \Carbon\Carbon::today()->format('Y-m-d'),
            'site_country' => $salesReport['site_country'],
            'penalty' => $salesReport['penalty'],
            'additional_payment' => $salesReport['additional_payment'],
            'srid' => $salesReport['srid'],
            'date_from' => $salesReport['date_from'],
            'date_to' => $salesReport['date_to']
        ];
    }
}
