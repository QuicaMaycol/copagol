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
            $table->string('apellido')->after('nombre');
            $table->integer('numero_camiseta')->nullable()->after('dni');
            $table->string('posicion')->nullable()->after('numero_camiseta');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('jugadores', function (Blueprint $table) {
            $table->dropColumn('apellido');
            $table->dropColumn('numero_camiseta');
            $table->dropColumn('posicion');
        });
    }
};
