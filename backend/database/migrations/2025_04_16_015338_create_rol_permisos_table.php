<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('rol_permisos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('rol_id');
            $table->unsignedBigInteger('permiso_id');
            $table->foreign('rol_id')->references('id')->on('roles')->onDelete('cascade');
            $table->foreign('permiso_id')->references('idPermiso')->on('permisos')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rol_permisos');
    }
};
