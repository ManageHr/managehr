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
        Schema::create('hojasvida', function (Blueprint $table) {
            $table->increments('idHojaDeVida');  
            $table->string('claseLibretaMilitar', 45);
            $table->string('numeroLibretaMilitar', 45);
            $table->integer('usuarioNumDocumento')->index('fk_hojasvida_usuarios');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hojasvidas');
    }
};
