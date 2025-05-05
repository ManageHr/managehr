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
        Schema::create('pazysalvo', function (Blueprint $table) {
            $table->integer('idPazSalvo')->primary();
            $table->string('descrip', 500);
            $table->string('firma', 45);
            $table->integer('contratoId')->index('fk_ContratoId_pazYSalvo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pazysalvos');
    }
};
