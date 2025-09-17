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
        Schema::create('partidos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campeonato_id')->constrained()->onDelete('cascade');
            
            $table->foreignId('equipo_local_id')->constrained('equipos')->onDelete('cascade');
            $table->foreignId('equipo_visitante_id')->constrained('equipos')->onDelete('cascade');
            
            $table->integer('goles_local')->nullable();
            $table->integer('goles_visitante')->nullable();
            
            $table->dateTime('fecha_partido');
            $table->enum('estado', ['programado', 'jugado', 'cancelado'])->default('programado');
            $table->integer('jornada')->nullable(); // Round number

            $table->string('ubicacion_partido')->nullable(); // To store the location of the match

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('partidos');
    }
};
