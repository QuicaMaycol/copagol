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
        Schema::create('campeonatos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Organizador
            $table->string('nombre_torneo');
            $table->integer('equipos_max');
            $table->integer('jugadores_por_equipo_max');
            $table->enum('tipo_futbol', ['5', '7', '11']);
            $table->enum('estado_torneo', ['inscripciones_abiertas', 'en_curso', 'finalizado', 'cancelado'])->default('inscripciones_abiertas');
            $table->enum('ubicacion_tipo', ['unica', 'equipo_local']);
            $table->string('cancha_unica_direccion')->nullable();
            $table->enum('privacidad', ['publico', 'privado'])->default('publico');
            $table->enum('reglamento_tipo', ['pdf', 'texto']);
            $table->string('reglamento_path')->nullable();
            $table->text('reglamento_texto')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('campeonatos');
    }
};
