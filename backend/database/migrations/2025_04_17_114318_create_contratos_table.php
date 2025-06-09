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
        Schema::create('contrato', function (Blueprint $table) {
            $table->integer('idContrato')->primary();
            $table->integer('estado');
            $table->date('fechaIngreso');
            $table->date('fechaFinalizacion');
            $table->string('archivo');
            $table->integer('tipoContratoId')->index('fk_contrato_tipocontrato');
            $table->integer('hojaDeVida')->index('fk_contrato_hojadevida');
            $table->integer('area')->index('fk_contrato_area');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contrato');
    }
};
