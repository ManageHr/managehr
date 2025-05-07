<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('homes', function (Blueprint $table) {
            $table->string('numDocumento')->primary();
            $table->string('primerNombre');
            $table->string('segundoNombre')->nullable();
            $table->string('primerApellido');
            $table->string('segundoApellido')->nullable();
            $table->date('fechaNac')->nullable();
            $table->integer('numHijos')->nullable();
            $table->string('contactoEmergencia')->nullable();
            $table->string('numContactoEmergencia')->nullable();
            $table->string('email')->unique();
            $table->string('direccion')->nullable();
            $table->string('telefono')->nullable();
            $table->unsignedBigInteger('nacionalidadId')->nullable();
            $table->unsignedBigInteger('epsCodigo')->nullable();
            $table->unsignedBigInteger('generoId')->nullable();
            $table->unsignedBigInteger('tipoDocumentoId')->nullable();
            $table->unsignedBigInteger('estadoCivilId')->nullable();
            $table->unsignedBigInteger('pensionesCodigo')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('homes');
    }
};
