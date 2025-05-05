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
        Schema::create('usuarios', function (Blueprint $table) {
            $table->integer('numDocumento')->primary();
            $table->string('primerNombre', 30);
            $table->string('segundoNombre', 30)->nullable();
            $table->string('primerApellido', 30);
            $table->string('segundoApellido', 30)->nullable();
            $table->binary('password');
            $table->date('fechaNac');
            $table->tinyInteger('numHijos')->nullable();
            $table->string('contactoEmergencia', 30);
            $table->string('numContactoEmergencia',20);
            $table->string('email', 100);
            $table->string('direccion', 45);
            $table->string('telefono',20);
            $table->integer('nacionalidadId')->index('fk_usuario_nacionalidad');
            $table->char('epsCodigo', 6)->index('fk_usuario_eps');
            $table->integer('generoId')->index('fk_usuario_genero');
            $table->integer('tipoDocumentoId')->index('fk_usuario_tipodedocumento');
            $table->integer('estadoCivilId')->index('fk_usuario_estadocivil');
            $table->char('pensionesCodigo', 6)->index('fk_usuario_pensiones');
            $table->integer('usersId', 6)->index('fk_usuario_users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('usuarios');
    }
};
