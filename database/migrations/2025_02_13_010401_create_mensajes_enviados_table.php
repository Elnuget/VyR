<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('mensajes_enviados', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('historial_id');
            $table->string('tipo');
            $table->text('mensaje');
            $table->timestamp('fecha_envio')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('historial_id')
                  ->references('id')
                  ->on('historiales_clinicos')
                  ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('mensajes_enviados');
    }
}; 