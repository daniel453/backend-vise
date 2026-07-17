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
        Schema::create('bulletins', function (Blueprint $table) {
            $table->id();
            $table->string('scope')->comment('"NACIONAL" o el nombre de la regional cubierta (ej. "SUR", "OCCIDENTE")');
            $table->string('mode')->comment('"GENERAL" (todo el país) o "REGIONAL" (una sola regional)');
            $table->string('headline')->nullable()->comment('Titular del boletín (ej. "SECUESTROS MASIVOS EN CHOCÓ")');
            $table->unsignedInteger('total_events')->default(0);
            $table->unsignedInteger('critical_events')->default(0);
            $table->unsignedInteger('high_impact_events')->default(0);
            $table->unsignedInteger('regions_affected')->default(0);
            $table->unsignedInteger('roads_affected')->default(0);
            $table->unsignedInteger('electoral_events')->default(0);
            $table->text('main_threat')->nullable()->comment('Amenaza principal del período');
            $table->text('critical_zone')->nullable()->comment('Zona(s) crítica(s) del período');
            $table->string('trend')->nullable()->comment('ALTA, MEDIA o BAJA');
            $table->unsignedInteger('sources_consulted')->default(0);
            $table->text('electoral_context')->nullable();
            $table->text('logistics_recommendation')->nullable();
            $table->text('perimeter_recommendation')->nullable();
            $table->text('operational_recommendation')->nullable();
            $table->text('digital_recommendation')->nullable();
            $table->json('heat_map')->nullable()->comment('Array [{department, code, lat, lng, level, events, main_type}] para el mapa de calor');
            $table->json('distribution')->nullable()->comment('Array [{city, events}] para la sección de Distribución');
            $table->timestamp('generated_at')->comment('Fecha/hora que reporta el boletín (Colombia)');
            $table->timestamps();

            $table->index(['scope', 'generated_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bulletins');
    }
};
