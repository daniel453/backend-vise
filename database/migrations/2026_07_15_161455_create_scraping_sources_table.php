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
        Schema::create('scraping_sources', function (Blueprint $table) {
            $table->id();
            $table->string('group')->comment('Categoría de la matriz de fuentes (ej. "Policía", "Medios nacionales")');
            $table->string('source')->comment('Nombre de la fuente puntual (ej. "El Tiempo", "DIJIN · Revista Criminalidad")');
            $table->string('coverage')->nullable()->comment('Alcance geográfico de la fuente (ej. "Nacional, regional")');
            $table->string('domain')->nullable()->comment('Dominio web para consultas site: (solo aplica a medios indexados en Google News)');
            $table->unsignedInteger('sort_order')->default(0)->comment('Posición original en la matriz, para mantener el mismo orden al listar');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('scraping_sources');
    }
};
