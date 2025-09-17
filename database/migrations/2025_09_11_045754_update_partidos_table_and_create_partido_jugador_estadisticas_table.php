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
        Schema::table('partidos', function (Blueprint $table) {
            // Change 'estado' column to allow new enum values
            $table->enum('estado', ['pendiente', 'en_juego', 'finalizado', 'suspendido', 'reprogramado', 'cancelado'])
                  ->default('pendiente')
                  ->change();
        });

        Schema::create('partido_jugador_estadisticas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('partido_id')->constrained('partidos')->onDelete('cascade');
            $table->foreignId('jugador_id')->constrained('jugadores')->onDelete('cascade');
            $table->integer('goles')->default(0);
            $table->integer('asistencias')->default(0);
            $table->integer('tarjetas_amarillas')->default(0);
            $table->integer('tarjetas_rojas')->default(0);
            $table->timestamps();

            $table->unique(['partido_id', 'jugador_id'], 'partido_jugador_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('partido_jugador_estadisticas');

        Schema::table('partidos', function (Blueprint $table) {
            // Revert 'estado' column to its original enum values
            $table->enum('estado', ['programado', 'jugado', 'cancelado'])
                  ->default('programado')
                  ->change();
        });
    }
};