<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('wb_exports', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->integer('user_id');
            $table->integer('account_id');
            $table->string('type');
            $table->dateTime('start_at');
            $table->dateTime('finish_at')->nullable();
            $table->integer('status')->default(0);

            $table->index('user_id');
            $table->index('account_id');
            $table->index('type');
            $table->index('start_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('wb_exports');
    }
};
