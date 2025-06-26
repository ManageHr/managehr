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
        Schema::create('experiencialaboral', function (Blueprint $table) {
            $table->id('idExperiencia'); // PRIMARY KEY auto_increment
            $table->string('nomEmpresa', 45);
            $table->string('nombJefe', 45);
            $table->integer('telefono'); // puedes cambiarlo a string si usas indicativos o caracteres especiales
            $table->string('cargo', 20);
            $table->text('actividades');
            $table->date('fechaInicio');
            $table->date('fechaFinalizacion');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('experiencialaboral');
    }
};
