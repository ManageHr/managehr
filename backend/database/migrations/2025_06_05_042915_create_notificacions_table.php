<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;


return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('notificaciones', function (Blueprint $table) {
            $table->id('idNotificacion');

            $table->enum('tipo', ['HorasExtra', 'Vacaciones', 'Permiso', 'Postulacion', 'Rol']);
            $table->enum('accion', ['Creado', 'Modificado', 'Eliminado', 'EstadoAceptado']);

            $table->text('detalle')->nullable();

            $table->unsignedBigInteger('usuarioId')->nullable();
            $table->unsignedBigInteger('referenciaId')->nullable();
            $table->unsignedBigInteger('areaId')->nullable();

            $table->dateTime('fecha')->default(DB::raw('CURRENT_TIMESTAMP'));

            
            $table->foreign('usuarioId')->references('usersId')->on('usuarios')->onDelete('set null');
            $table->foreign('areaId')->references('idArea')->on('areas')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notificacions');
    }
};
