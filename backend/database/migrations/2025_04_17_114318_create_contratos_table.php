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
            $table->integer('idContrato');
            $table->integer('estado');
            $table->date('fechaIngreso');
            $table->date('fechaFinal');
            $table->string('documento');
            $table->integer('tipoContratoId')->index('fk_tipoContratoId_contrato');
            $table->integer('numDocumento')->index('fk_numDocumento_contrato');
            $table->integer('areaId');
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
