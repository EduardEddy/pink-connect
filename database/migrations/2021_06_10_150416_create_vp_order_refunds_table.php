<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVpOrderRefundsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vp_order_refunds', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id');
            $table->timestamp("date")->nullable();
            $table->double("productCost");
            $table->double("shippingCost");
            $table->string("currency")->default('EUR');
            $table->string('type')->nullable();
            $table->timestamps();
            $table->foreign('order_id')->references('id')->on('vp_orders')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('vp_order_refunds');
    }
}
