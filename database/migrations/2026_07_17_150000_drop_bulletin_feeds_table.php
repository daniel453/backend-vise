<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Se elimina bulletin_feeds: era la tabla de búsquedas de Google News del
// enfoque anterior. Ahora el workflow lee el RSS propio de cada fuente desde
// scraping_sources.rss_url, así que esta tabla quedó sin uso.
return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('bulletin_feeds');
    }

    public function down(): void
    {
        Schema::create('bulletin_feeds', function (Blueprint $table) {
            $table->id();
            $table->string('label')->comment('Nombre legible del feed (ej. "ELN combate operativo")');
            $table->string('feed_type')->comment('"google_news" (búsqueda temática) o "direct" (URL de RSS directa)');
            $table->text('query')->nullable()->comment('Términos de búsqueda para Google News — solo para feed_type=google_news');
            $table->text('url')->nullable()->comment('URL completa del RSS — solo para feed_type=direct');
            $table->string('category')->nullable()->comment('Pista de categoría; la clasificación real la hace el flujo');
            $table->boolean('active')->default(true)->comment('El flujo solo consulta los feeds activos');
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index('active');
        });
    }
};
