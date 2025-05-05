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
        Schema::create('estudios', function (Blueprint $table) {
            $table->id('idEstudios');
            $table->string('nomEstudio', 45);
            $table->string('nomInstitucion', 50);
            $table->string('tituloObtenido', 45);
            $table->year('añoFinalizacion');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('estudios');
    }
};
