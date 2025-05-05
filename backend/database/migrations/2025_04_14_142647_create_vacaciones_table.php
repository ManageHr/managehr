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
        Schema::create('vacaciones', function (Blueprint $table) {
            $table->integer('idVacaciones')->primary();
            $table->string('motivo', 500);
            $table->date('fechaInicio');
            $table->date('fechaFinal');
            $table->integer('dias');
            $table->enum('estado',['Pendiente','Aprobado','rechazado'])->default('Pendiente');
            $table->integer('contratoId')->index('fk_ContratoId_incapacidad');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vacaciones');
    }
};
