<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Ciudades que el workflow de MARCHAS monitorea (administrable, como las
// fuentes/temas). El flujo n8n lee esta tabla; agregar/quitar una ciudad no
// toca el workflow.
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('march_cities', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique()->comment('Ciudad monitoreada para marchas (ej. "Bogotá")');
            $table->boolean('active')->default(true)->comment('Si el workflow la incluye en la búsqueda');
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('march_cities');
    }
};
