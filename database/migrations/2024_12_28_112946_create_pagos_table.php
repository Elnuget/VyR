<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePagosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pagos', function (Blueprint $table) {
            $table->id();
            
            $table->unsignedBigInteger('mediodepago_id');
            $table->foreign('mediodepago_id')->references('id')->on('mediosdepagos');
            $table->unsignedBigInteger('pedido_id'); // Changed from 'pedidos_id' to 'pedido_id'
            $table->foreign('pedido_id')->references('id')->on('pedidos'); // Changed from 'pedidos_id' to 'pedido_id'
            $table->decimal('pago', 10, 2);  // Changed from integer to decimal(10,2)
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
        Schema::dropIfExists('pagos');
    }
}
