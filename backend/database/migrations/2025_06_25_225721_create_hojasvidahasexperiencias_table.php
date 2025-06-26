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
        Schema::create('hojasvidahasexperiencias', function (Blueprint $table) {
            $table->integer('idHasexperirencia')->primary();
            $table->integer('estado');
            $table->string('archivo');
            $table->integer('idHojaDevida')->index('fk_hojasvidahasexperiencia_hojadevida');
            $table->integer('idExperiencia')->index('fk_hojasvidahasexperiencia_experiencia');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hojasvidahasexperiencias');
    }
};
