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
        Schema::create('permisos', function (Blueprint $table) {
            $table->integer('idPermiso')->primary();
            $table->string('desc', 500);
            $table->date('fechaInicio');
            $table->date('fechaFinal');
            $table->integer('estado');
            $table->integer('tipoPermisoId')->index('fk_tipoPermiso_permisos');
            $table->integer('contratoId')->index('fk_ContratoId_permisos');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('permisos');
    }
};
