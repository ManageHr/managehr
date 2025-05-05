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
        Schema::create('trazabilidad', function (Blueprint $table) {
            $table->id('idTrazabilidad'); // PRIMARY KEY auto_increment
            $table->date('fechamodificacion', 45);
            $table->string('ip', 100);
            $table->string('usuarioanterior');
            $table->string('usuarionuevo');
            $table->string('claveAnterior');
            $table->string('claveNueva');
            $table->integer('numDocumento');
             
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trazabilidads');
    }
};
