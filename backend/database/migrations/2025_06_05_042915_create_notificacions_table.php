<?php 
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateNotificacionesTable extends Migration
{
    public function up(): void
    {
        Schema::create('notificaciones', function (Blueprint $table) {
            $table->id('idNotificacion');

            $table->enum('tipo', ['HorasExtra', 'Vacaciones', 'Permiso', 'Postulacion', 'Rol']);
            $table->enum('accion', ['Creado', 'Modificado', 'Eliminado', 'EstadoAceptado']);
            $table->text('detalle')->nullable();
            $table->dateTime('fecha')->default(DB::raw('CURRENT_TIMESTAMP'));

            $table->unsignedInteger('estado')->default(0);
            $table->unsignedBigInteger('referenciaId')->nullable();

            // Nuevo campo de relación con contratos
            $table->unsignedBigInteger('contratoId')->nullable();

            // Clave foránea a contratos
            $table->foreign('contratoId')
                  ->references('idContrato')
                  ->on('contratos')
                  ->onDelete('set null')
                  ->onUpdate('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notificaciones');
    }
}
