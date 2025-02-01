<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCalificacionToPedidosTable extends Migration
{
    public function up()
    {
        Schema::table('pedidos', function (Blueprint $table) {
            $table->integer('calificacion')->nullable();
            $table->text('comentario_calificacion')->nullable();
            $table->timestamp('fecha_calificacion')->nullable();
        });
    }

    public function down()
    {
        Schema::table('pedidos', function (Blueprint $table) {
            $table->dropColumn([
                'calificacion',
                'comentario_calificacion',
                'fecha_calificacion'
            ]);
        });
    }
}