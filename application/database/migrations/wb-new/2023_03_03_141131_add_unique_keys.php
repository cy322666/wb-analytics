<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('wb_incomes', function (Blueprint $table) {
//            $table->index('wb_incomes_foreign');
            $table->dropUnique('wb_incomes_unique');
            $table->unique(['income_id', 'barcode'], 'wb_incomes_unique');
        });

        Schema::table('wb_orders', function (Blueprint $table) {
//            $table->index('wb_orders_foreign');
            $table->dropUnique('wb_orders_unique');
            $table->unique(['odid'], 'wb_orders_unique');
        });

        Schema::table('wb_sales', function (Blueprint $table) {
//            $table->index( 'wb_sales_foreign');
            $table->dropUnique('wb_sales_unique');
            $table->unique(['sale_id'], 'wb_sales_unique');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('wb_incomes', function (Blueprint $table) {
            $table->dropUnique('wb_incomes_unique');
            $table->unique(['income_id', 'date', 'last_change_date', 'barcode', 'status'], 'wb_incomes_unique');
            $table->dropIndex('wb_incomes_foreign');
        });

        Schema::table('wb_orders', function (Blueprint $table) {
            $table->dropUnique('wb_orders_unique');
            $table->unique(['date', 'last_change_date', 'barcode', 'odid', 'g_number', 'is_cancel'], 'wb_orders_unique');
            $table->dropIndex('wb_orders_foreign');
        });

        Schema::table('wb_sales', function (Blueprint $table) {
            $table->dropUnique('wb_sales_unique');
            $table->unique(['date', 'last_change_date', 'barcode', 'sale_id', 'odid', 'g_number'], 'wb_sales_unique');
            $table->dropIndex('wb_sales_foreign');
        });
    }
};
