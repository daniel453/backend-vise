<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Se revive bulletin_feeds para el enfoque HÍBRIDO: además de leer el RSS propio
// de las fuentes confiables (scraping_sources), el workflow también busca por
// TEMA en Google News (grupos armados, orden público, vial, ambiental…) para no
// perderse eventos de zonas de conflicto que los medios de la lista no cubren.
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('bulletin_feeds')) {
            return;
        }

        Schema::create('bulletin_feeds', function (Blueprint $table) {
            $table->id();
            $table->string('label')->comment('Nombre legible del tema (ej. "ELN Colombia combate operativo")');
            $table->string('feed_type')->default('google_news')->comment('"google_news" (búsqueda por tema) o "direct" (URL de RSS directa)');
            $table->text('query')->nullable()->comment('Términos de búsqueda para Google News — solo para feed_type=google_news');
            $table->text('url')->nullable()->comment('URL completa del RSS — solo para feed_type=direct');
            $table->string('category')->nullable()->comment('Pista de categoría (grupos_armados, orden_publico, vial, ambiental, protesta, operativos, electoral)');
            $table->boolean('active')->default(true)->comment('El flujo solo consulta los temas activos');
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index('active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bulletin_feeds');
    }
};
