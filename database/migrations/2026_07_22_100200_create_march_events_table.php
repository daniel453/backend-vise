<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Una marcha/movilización por fila (para el boletín temático de marchas). Las
// llena el workflow n8n de marchas a partir de las noticias de las ciudades
// monitoreadas (march_cities). Dedup por source_url.
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('march_events', function (Blueprint $table) {
            $table->id();
            $table->string('batch_id')->nullable()->comment('Corrida del workflow a la que pertenece');
            $table->string('city')->comment('Ciudad de la marcha (una de march_cities)');
            $table->string('title');
            $table->date('event_date')->nullable()->comment('Fecha de la marcha');
            $table->string('event_time')->nullable()->comment('Hora de inicio/concentración, texto libre (ej. "10:00 a. m.")');
            $table->string('convener')->nullable()->comment('Convocante (sindicato, gremio, colectivo, etc.)');
            $table->string('concentration_point')->nullable()->comment('Punto de concentración');
            $table->text('route')->nullable()->comment('Recorrido/ruta previsto');
            $table->text('affected_roads')->nullable()->comment('Vías/corredores afectados');
            $table->string('level')->nullable()->comment('Impacto esperado: ALTO, MEDIO o BAJO');
            $table->text('summary')->nullable();
            $table->string('media_outlet')->nullable();
            $table->string('source_url')->comment('Link de la noticia — clave de deduplicación');
            $table->json('details')->nullable();
            $table->timestamps();

            $table->unique('source_url');
            $table->index(['batch_id', 'city']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('march_events');
    }
};
