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
        Schema::table('jugadores', function (Blueprint $table) {
            $table->integer('goles')->default(0)->after('posicion');
            $table->integer('tarjetas_amarillas')->default(0)->after('goles');
            $table->integer('tarjetas_rojas')->default(0)->after('tarjetas_amarillas');
            $table->boolean('suspendido')->default(false)->after('tarjetas_rojas');
            $table->unsignedTinyInteger('valoracion_general')->default(50)->after('suspendido')->comment('Valoración del 1 al 100');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('jugadores', function (Blueprint $table) {
            $table->dropColumn(['goles', 'tarjetas_amarillas', 'tarjetas_rojas', 'suspendido', 'valoracion_general']);
        });
    }
};