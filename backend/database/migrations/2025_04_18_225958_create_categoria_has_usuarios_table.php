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
        Schema::create('categoriavacanteshasusuario', function (Blueprint $table) {
            $table->integer('categoriaVacantesId')->index('fk_categoriavacantes_usuarios');
            $table->integer('usuarioNumDocumento')->index('fk_usuarios_categoriavacantes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categoriavacanteshasusuario');
    }
};
