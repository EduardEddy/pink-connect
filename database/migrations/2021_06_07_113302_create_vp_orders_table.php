<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVpOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vp_orders', function (Blueprint $table) {
            $table->id();
            $table->string('marketplaceName')->nullable();
            $table->string('marketplaceCode')->nullable();
            $table->string('marketplaceOrderCode')->nullable();
            $table->integer("shopChannelId")->nullable();
            $table->string('shopChannelName')->nullable();
            $table->enum('status', ['WAITING_ACCEPTANCE','PENDING', 'PROCESSING', 'SHIPPED','CANCELLED'])->default('WAITING_ACCEPTANCE');
            $table->timestamp('shippedOrderDate')->nullable();
            $table->double('totalPrice')->nullable();
            $table->double('shippingCosts')->nullable();
            $table->double('shippingTaxRate')->nullable();
            $table->string('currency')->nullable();
            $table->string('requestedShippingMethod')->nullable();
            $table->string('deliveryNote')->nullable();
            $table->string('pickupPointId')->nullable();
            $table->timestamps();
            $table->boolean('updated')->default(true);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('vp_orders');
    }
}
