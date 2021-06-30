<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVpOrderDeliveryDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vp_order_delivery_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id');
            $table->string("carrierId")->nullable();
            $table->string("carrierName")->nullable();
            $table->string("trackingNumber")->nullable();
            $table->string("trackingUrl")->nullable();
            $table->timestamps();
            $table->boolean('updated')->default(true);
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
        Schema::dropIfExists('vp_order_delivery_details');
    }
}
