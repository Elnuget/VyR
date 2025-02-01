<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSoftDeletesToAllTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $tables = [
            'admins',
            'cash_histories',
            'caja',
            'clientes',
            'inventarios',
            'historiales_clinicos',
            'mediosdepagos',
            'pagos',
            'pedidos',
            'pedido_lunas',
            'users',
            'pedido_inventario'
        ];

        foreach ($tables as $tableName) {
            if (Schema::hasTable($tableName) && !Schema::hasColumn($tableName, 'deleted_at')) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->softDeletes();
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $tables = [
            'admins',
            'cash_histories',
            'caja',
            'clientes',
            'inventarios',
            'historiales_clinicos',
            'mediosdepagos',
            'pagos',
            'pedidos',
            'pedido_lunas',
            'users',
            'pedido_inventario'
        ];

        foreach ($tables as $tableName) {
            if (Schema::hasTable($tableName) && Schema::hasColumn($tableName, 'deleted_at')) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->dropSoftDeletes();
                });
            }
        }
    }
}
