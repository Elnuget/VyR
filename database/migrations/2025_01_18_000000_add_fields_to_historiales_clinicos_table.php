<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldsToHistorialesClinicosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('historiales_clinicos', function (Blueprint $table) {
            if (!Schema::hasColumn('historiales_clinicos', 'cedula')) {
                $table->string('cedula', 50)->nullable();
            }
            if (!Schema::hasColumn('historiales_clinicos', 'ph_od')) {
                $table->string('ph_od', 50)->nullable();
            }
            if (!Schema::hasColumn('historiales_clinicos', 'ph_oi')) {
                $table->string('ph_oi', 50)->nullable();
            }
            if (!Schema::hasColumn('historiales_clinicos', 'add')) {
                $table->string('add', 50)->nullable();
            }
            if (!Schema::hasColumn('historiales_clinicos', 'cotizacion')) {
                $table->text('cotizacion')->nullable();
            }
            if (!Schema::hasColumn('historiales_clinicos', 'usuario_id')) {
                $table->unsignedBigInteger('usuario_id')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('historiales_clinicos', function (Blueprint $table) {
            $columns = ['cedula', 'ph_od', 'ph_oi', 'add', 'cotizacion', 'usuario_id'];
            foreach ($columns as $column) {
                if (Schema::hasColumn('historiales_clinicos', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
}