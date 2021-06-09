<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUpdatedToVpPricesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('vp_prices', function (Blueprint $table) {
            $table->boolean('updated')->default(false)->comment('cuando se crea un registro este sera falso al hacer una comparacion con los datos de pink conect actualiza a true y si se actualiza un dato en esta tabla actualizar de nuevo a falso');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('vp_prices', function (Blueprint $table) {
            $table->dropColumn('updated');
            $table->dropColumn('created_at');
            $table->dropColumn('updated_at');
        });
    }
}
