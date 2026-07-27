<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Los feeds RSS (sobre todo Google News) devuelven URLs de redirección de
     * más de 255 caracteres, y los títulos/convocantes/puntos vienen de fuera de
     * la aplicación: no deben tumbar una corrida del boletín de marchas. Se
     * amplían a text los campos de texto externo (mismo criterio que
     * bulletin_events).
     */
    public function up(): void
    {
        Schema::table('march_events', function (Blueprint $table) {
            $table->text('title')->change();
            $table->text('convener')->nullable()->change();
            $table->text('concentration_point')->nullable()->change();
            $table->text('media_outlet')->nullable()->change();
            $table->text('source_url')->change();
        });
    }

    public function down(): void
    {
        Schema::table('march_events', function (Blueprint $table) {
            $table->string('title')->change();
            $table->string('convener')->nullable()->change();
            $table->string('concentration_point')->nullable()->change();
            $table->string('media_outlet')->nullable()->change();
            $table->string('source_url')->change();
        });
    }
};
