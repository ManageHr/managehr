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
        Schema::create('incapacidad', function (Blueprint $table) {
            $table->integer('idIncapacidad')->primary();
            $table->string('descrip', 500);
            $table->string('archivo', 45);
            $table->date('fechaInicio');
            $table->date('fechaFinal');
            $table->integer('contratoId')->index('fk_ContratoId_incapacidad');
            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('incapacidads');
    }
};
