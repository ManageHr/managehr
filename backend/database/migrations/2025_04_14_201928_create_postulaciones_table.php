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
        Schema::create('postulaciones', function (Blueprint $table) {
            $table->integer('idPostulaciones')->primary();
            $table->date('fechaPostulacion');
            $table->integer('estado');
            $table->integer('vacantesId');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('postulaciones');
    }
};
