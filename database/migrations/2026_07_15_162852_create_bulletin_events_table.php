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
        Schema::create('bulletin_events', function (Blueprint $table) {
            $table->id();
            $table->string('type')->comment('"security" (grupos armados, homicidios, orden público), "electoral", "traffic" (vías/TransMilenio) o "environmental" (alertas ambientales)');
            $table->string('severity')->nullable()->comment('Nivel de criticidad: CRÍTICO, ALTO, MEDIO o BAJO');
            $table->string('subtype')->nullable()->comment('Subtipo puntual del evento (ej. CAPTURA, SECUESTRO, DERRUMBE, CERRADO) — el vocabulario varía según "type"');
            $table->boolean('is_transmilenio')->default(false)->comment('Solo aplica a type=traffic — separa la tabla de estaciones de TransMilenio del resto de vías en el boletín');
            $table->string('region')->nullable()->comment('Región natural (Andina, Caribe, Pacífica, Orinoquía, Amazonía) — resuelta desde el departamento al insertar');
            $table->string('department')->comment('Departamento detectado en la noticia (ej. "Antioquia")');
            $table->string('municipality')->nullable()->comment('Municipio detectado en el título/resumen, si se identificó alguno');
            $table->string('title');
            $table->text('summary')->nullable();
            $table->string('media_outlet')->nullable()->comment('Medio que publicó la noticia (ej. "El Tiempo")');
            $table->string('source_url')->comment('Link de la noticia (o de redirección de Google News) — clave de deduplicación');
            $table->timestamp('published_at')->nullable();
            $table->json('details')->nullable()->comment('Datos propios de cada "type" que no ameritan columna aparte (ej. en vías: route_name, route_number, status, alternative_route, estimated_time)');
            $table->timestamps();

            $table->unique('source_url');
            $table->index(['region', 'department', 'municipality']);
            $table->index('type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bulletin_events');
    }
};
