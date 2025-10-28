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
        Schema::create('hojasvidahasestudios', function (Blueprint $table) {
            $table->integer('idHasestudios')->primary();
            $table->integer('estado');
            $table->string('archivo');
            $table->integer('idHojaDeVida')->index('fk_hojasvidahasestudios_hojadevida');
            $table->integer('idEstudios')->index('fk_hojasvidahasestudios_estudios');
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hojasvidahasestudios');
    }
};
