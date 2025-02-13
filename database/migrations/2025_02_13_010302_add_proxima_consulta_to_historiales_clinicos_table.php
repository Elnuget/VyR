<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('historiales_clinicos', function (Blueprint $table) {
            $table->date('proxima_consulta')->nullable()->after('tratamiento');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('historiales_clinicos', function (Blueprint $table) {
            $table->dropColumn('proxima_consulta');
        });
    }
};
