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
        Schema::create('bulletin_feeds', function (Blueprint $table) {
            $table->id();
            $table->string('label')->comment('Nombre legible del feed (ej. "ELN combate operativo")');
            $table->string('feed_type')->comment('"google_news" (búsqueda temática) o "direct" (URL de RSS directa)');
            $table->text('query')->nullable()->comment('Términos de búsqueda para Google News — solo para feed_type=google_news');
            $table->text('url')->nullable()->comment('URL completa del RSS — solo para feed_type=direct');
            $table->string('category')->nullable()->comment('Pista de categoría (grupos_armados, orden_publico, vial, ambiental, electoral, protesta) — la clasificación real la hace el flujo, esto es solo referencia');
            $table->boolean('active')->default(true)->comment('El flujo de n8n solo consulta los feeds activos');
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index('active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bulletin_feeds');
    }
};
